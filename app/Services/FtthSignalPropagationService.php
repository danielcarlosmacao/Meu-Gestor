<?php

namespace App\Services;

use App\Models\FtthCableFiberBox;
use App\Models\FtthFiberCable;
use App\Models\FtthFiberFusion;
use App\Models\FtthSplinter;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class FtthSignalPropagationService
{
    /**
     * Tolerância usada para comparar valores decimais.
     */
    private const POWER_TOLERANCE = 0.001;

    /**
     * Proteção contra uma topologia incorreta que gere propagação excessiva.
     */
    private const MAX_PROPAGATIONS = 10000;

    /**
     * Fibras indexadas pelo ID.
     *
     * @var array<int, FtthFiberCable>
     */
    private array $fibersById = [];

    /**
     * Fusões indexadas pela fibra de origem.
     *
     * @var array<int, array<int, FtthFiberFusion>>
     */
    private array $fusionsBySource = [];

    /**
     * Splitters indexados pela fibra de entrada.
     *
     * @var array<int, array<int, FtthSplinter>>
     */
    private array $splittersByInput = [];

    /**
     * Saídas indexadas pelo ID do splitter.
     *
     * @var array<int, array<int, FtthFiberCable>>
     */
    private array $splitterOutputs = [];

    /**
     * Cabos indexados pelo ID.
     *
     * @var array<int, FtthCableFiberBox>
     */
    private array $cablesById = [];

    /**
     * Fibras indexadas por:
     *
     * cabo -> CTO -> identificação.
     *
     * @var array<int, array<int, array<string, FtthFiberCable>>>
     */
    private array $fibersByCableBoxIdentification = [];

    /**
     * Última potência processada em cada fibra.
     *
     * @var array<int, float>
     */
    private array $processedPower = [];

    /**
     * Quantidade de etapas executadas durante uma propagação.
     */
    private int $propagationCount = 0;

    /**
     * Recalcula a rede a partir das fibras de origem de uma CTO.
     *
     * Use este método para manter a antiga rota de recálculo:
     *
     * /fiber-box/{id}/recalculate-local
     */
    public function recalculateBox(int $boxId): int
    {
        return DB::transaction(function () use ($boxId): int {
            $this->loadTopology();
            $this->resetRuntime();

            $origins = $this->findOriginFibers($boxId);

            foreach ($origins as $fiber) {
                if ($fiber->optical_power === null) {
                    continue;
                }

                $this->propagate(
                    fiberId: (int) $fiber->id,
                    power: (float) $fiber->optical_power,
                    path: []
                );
            }

            return $this->propagationCount;
        });
    }

    /**
     * Propaga a partir de uma fibra que teve o sinal editado manualmente.
     *
     * O valor informado torna-se a nova referência naquele ponto da rede.
     */
    public function propagateFromFiber(
        int|FtthFiberCable $fiber,
        float $opticalPower
    ): int {
        return DB::transaction(function () use ($fiber, $opticalPower): int {
            $fiberId = $fiber instanceof FtthFiberCable
                ? (int) $fiber->id
                : (int) $fiber;

            $this->loadTopology();
            $this->resetRuntime();

            if (!isset($this->fibersById[$fiberId])) {
                throw new RuntimeException(
                    "Fibra FTTH de ID {$fiberId} não encontrada."
                );
            }

            /*
             * A fibra editada é atualizada mesmo que seja uma fibra de origem.
             */
            $this->updateFiberPower(
                fiberId: $fiberId,
                power: $opticalPower,
                force: true
            );

            /*
             * Inicia a cascata a partir da própria fibra editada.
             */
            $this->propagate(
                fiberId: $fiberId,
                power: $opticalPower,
                path: []
            );

            return $this->propagationCount;
        });
    }

    /**
     * Carrega a topologia ativa na memória.
     *
     * Não existem consultas dentro da recursão.
     */
    private function loadTopology(): void
    {
        $this->clearTopology();

        $fibers = FtthFiberCable::query()->get();

        foreach ($fibers as $fiber) {
            $fiberId = (int) $fiber->id;
            $boxId = (int) $fiber->fiber_box_id;

            $this->fibersById[$fiberId] = $fiber;

            if ($fiber->cable_fiber_box_id !== null) {
                $cableId = (int) $fiber->cable_fiber_box_id;
                $identification = $this->normalizeIdentification(
                    $fiber->fiber_identification
                );

                $this->fibersByCableBoxIdentification[$cableId][$boxId][$identification] = $fiber;
            }

            if ($fiber->splinter_out_id !== null) {
                $splinterId = (int) $fiber->splinter_out_id;

                $this->splitterOutputs[$splinterId][] = $fiber;
            }
        }

        $fusions = FtthFiberFusion::query()->get();

        foreach ($fusions as $fusion) {
            $sourceFiberId = (int) $fusion->fiber_cables_id_1;

            $this->fusionsBySource[$sourceFiberId][] = $fusion;
        }

        $splitters = FtthSplinter::query()
            ->with('loss')
            ->get();

        foreach ($splitters as $splitter) {
            $inputFiberId = (int) $splitter->splinter_input;

            $this->splittersByInput[$inputFiberId][] = $splitter;
        }

        $cables = FtthCableFiberBox::query()->get();

        foreach ($cables as $cable) {
            $this->cablesById[(int) $cable->id] = $cable;
        }
    }

    /**
     * Encontra as fibras que podem iniciar o recálculo dentro da CTO.
     *
     * Não são consideradas origens:
     * - saídas de splitter;
     * - fibras que são destino de uma fusão.
     */
    private function findOriginFibers(int $boxId): EloquentCollection
    {
        $destinationFiberIds = [];

        foreach ($this->fusionsBySource as $fusions) {
            foreach ($fusions as $fusion) {
                $destinationFiberIds[(int) $fusion->fiber_cables_id_2] = true;
            }
        }

        return new EloquentCollection(
            array_values(
                array_filter(
                    $this->fibersById,
                    static function (
                        FtthFiberCable $fiber
                    ) use ($boxId, $destinationFiberIds): bool {
                        if ((int) $fiber->fiber_box_id !== $boxId) {
                            return false;
                        }

                        if ($fiber->splinter_out_id !== null) {
                            return false;
                        }

                        if (isset($destinationFiberIds[(int) $fiber->id])) {
                            return false;
                        }

                        return true;
                    }
                )
            )
        );
    }

    /**
     * Percorre a rede a partir de uma fibra.
     *
     * Para cada fibra, são processadas três possibilidades:
     * 1. fusões;
     * 2. splitters;
     * 3. continuidade física do cabo.
     */
    private function propagate(
        int $fiberId,
        float $power,
        array $path
    ): void {
        $this->guardPropagationLimit();

        if (!isset($this->fibersById[$fiberId])) {
            return;
        }

        /*
         * Impede ciclos na mesma ramificação.
         *
         * Exemplo de cadastro incorreto:
         * F-01 -> F-02 -> F-01.
         */
        if (isset($path[$fiberId])) {
            return;
        }

        /*
         * Se essa fibra já foi processada com a mesma potência,
         * não há necessidade de percorrer tudo novamente.
         */
        if (
            isset($this->processedPower[$fiberId])
            && $this->samePower(
                $this->processedPower[$fiberId],
                $power
            )
        ) {
            return;
        }

        $path[$fiberId] = true;
        $this->processedPower[$fiberId] = $power;
        $this->propagationCount++;

        $this->updateFiberPower(
            fiberId: $fiberId,
            power: $power
        );

        $fiber = $this->fibersById[$fiberId];

        /*
         * 1. Segue todas as fusões cuja origem seja a fibra atual.
         */
        $this->propagateFusions(
            fiber: $fiber,
            power: $power,
            path: $path
        );

        /*
         * 2. Caso a fibra seja entrada de splitter,
         * calcula e segue todas as saídas.
         */
        $this->propagateSplitters(
            fiber: $fiber,
            power: $power,
            path: $path
        );

        /*
         * 3. Caso a fibra pertença a um cabo e esteja na CTO de origem,
         * segue para a fibra de mesma identificação na CTO de destino.
         */
        $this->propagateCable(
            fiber: $fiber,
            power: $power,
            path: $path
        );
    }

    /**
     * Propaga pelas fusões.
     *
     * A regra é direcional:
     *
     * fiber_cables_id_1 -> fiber_cables_id_2
     */
    private function propagateFusions(
        FtthFiberCable $fiber,
        float $power,
        array $path
    ): void {
        $fusions = $this->fusionsBySource[(int) $fiber->id] ?? [];

        foreach ($fusions as $fusion) {
            $destinationId = (int) $fusion->fiber_cables_id_2;

            if (!isset($this->fibersById[$destinationId])) {
                continue;
            }

            /*
             * Sua tabela atual não possui coluna de perda da fusão.
             * Por isso a perda é considerada zero.
             */
            $fusionLoss = 0.0;

            $this->propagate(
                fiberId: $destinationId,
                power: $power - $fusionLoss,
                path: $path
            );
        }
    }

    /**
     * Propaga pelas saídas dos splitters.
     */
    private function propagateSplitters(
        FtthFiberCable $fiber,
        float $power,
        array $path
    ): void {
        $splitters = $this->splittersByInput[(int) $fiber->id] ?? [];

        foreach ($splitters as $splitter) {
            if (!$splitter->loss) {
                continue;
            }

            $outputs = $this->splitterOutputs[(int) $splitter->id] ?? [];

            foreach ($outputs as $outputFiber) {
                $loss = $this->resolveSplitterLoss(
                    splitter: $splitter,
                    outputFiber: $outputFiber
                );

                $this->propagate(
                    fiberId: (int) $outputFiber->id,
                    power: $power - $loss,
                    path: $path
                );
            }
        }
    }

    /**
     * Propaga uma fibra pela continuidade física do cabo.
     *
     * Exemplo:
     *
     * cabo 10 / CTO 1 / F-01
     *               ↓
     * cabo 10 / CTO 2 / F-01
     *
     * A fusão pode posteriormente mudar de F-01 para F-02,
     * mas o transporte dentro do mesmo cabo preserva a identificação.
     */
    private function propagateCable(
        FtthFiberCable $fiber,
        float $power,
        array $path
    ): void {
        if ($fiber->cable_fiber_box_id === null) {
            return;
        }

        $cableId = (int) $fiber->cable_fiber_box_id;

        if (!isset($this->cablesById[$cableId])) {
            return;
        }

        $cable = $this->cablesById[$cableId];

        /*
         * A propagação do cabo segue somente no sentido:
         *
         * input_fiber_box_id -> output_fiber_box_id
         *
         * Isso evita o sinal voltar para a CTO anterior.
         */
        if (
            (int) $fiber->fiber_box_id
            !== (int) $cable->input_fiber_box_id
        ) {
            return;
        }

        if ($cable->output_fiber_box_id === null) {
            return;
        }

        $destinationBoxId = (int) $cable->output_fiber_box_id;
        $identification = $this->normalizeIdentification(
            $fiber->fiber_identification
        );

        $destinationFiber =
            $this->fibersByCableBoxIdentification[$cableId][$destinationBoxId][$identification]
            ?? null;

        if (!$destinationFiber) {
            return;
        }

        /*
         * O cabo ainda não possui campo de atenuação.
         * Portanto a perda do transporte é zero.
         */
        $cableLoss = 0.0;

        $this->propagate(
            fiberId: (int) $destinationFiber->id,
            power: $power - $cableLoss,
            path: $path
        );
    }

    /**
     * Calcula a perda correspondente à saída do splitter.
     */
    private function resolveSplitterLoss(
        FtthSplinter $splitter,
        FtthFiberCable $outputFiber
    ): float {
        $lossConfiguration = $splitter->loss;

        if (!$lossConfiguration) {
            return 0.0;
        }

        /*
         * Splitter balanceado:
         * todas as saídas utilizam loss1.
         */
        if ($lossConfiguration->splinter_type === 'balanced') {
            return (float) ($lossConfiguration->loss1 ?? 0);
        }

        /*
         * Splitter desbalanceado:
         *
         * OUT-1 utiliza loss1;
         * OUT-2 utiliza loss2;
         * demais saídas usam loss1 como fallback.
         */
        $outputNumber = $this->extractOutputNumber(
            $outputFiber->fiber_identification
        );

        if (
            $outputNumber === 2
            && $lossConfiguration->loss2 !== null
        ) {
            return (float) $lossConfiguration->loss2;
        }

        return (float) ($lossConfiguration->loss1 ?? 0);
    }

    /**
     * Extrai o número final da saída.
     *
     * Exemplos aceitos:
     *
     * OUT-1
     * OUT-01
     * SPLITTER-OUT-8
     */
    private function extractOutputNumber(?string $identification): int
    {
        if (
            $identification !== null
            && preg_match('/OUT[-_\s]?(\d+)$/i', $identification, $matches)
        ) {
            return (int) $matches[1];
        }

        return 1;
    }

    /**
     * Atualiza a potência da fibra apenas quando necessário.
     */
    private function updateFiberPower(
        int $fiberId,
        float $power,
        bool $force = false
    ): void {
        if (!isset($this->fibersById[$fiberId])) {
            return;
        }

        $fiber = $this->fibersById[$fiberId];

        $currentPower = $fiber->optical_power === null
            ? null
            : (float) $fiber->optical_power;

        if (
            !$force
            && $currentPower !== null
            && $this->samePower($currentPower, $power)
        ) {
            return;
        }

        $normalizedPower = round($power, 2);

        FtthFiberCable::query()
            ->whereKey($fiberId)
            ->update([
                'optical_power' => $normalizedPower,
                'updated_at' => now(),
            ]);

        /*
         * Mantém o objeto em memória sincronizado com o banco.
         */
        $fiber->optical_power = $normalizedPower;
        $this->fibersById[$fiberId] = $fiber;
    }

    /**
     * Compara duas potências considerando tolerância decimal.
     */
    private function samePower(float $first, float $second): bool
    {
        return abs($first - $second) < self::POWER_TOLERANCE;
    }

    /**
     * Normaliza a identificação para evitar diferença de caixa
     * e espaços acidentais.
     */
    private function normalizeIdentification(?string $identification): string
    {
        return mb_strtoupper(trim((string) $identification));
    }

    /**
     * Proteção contra uma quantidade excessiva de etapas.
     */
    private function guardPropagationLimit(): void
    {
        if ($this->propagationCount >= self::MAX_PROPAGATIONS) {
            throw new RuntimeException(
                'A propagação FTTH ultrapassou o limite de segurança. '
                    . 'Verifique se existem fusões ou cabos formando um ciclo.'
            );
        }
    }

    /**
     * Limpa os índices da topologia.
     */
    private function clearTopology(): void
    {
        $this->fibersById = [];
        $this->fusionsBySource = [];
        $this->splittersByInput = [];
        $this->splitterOutputs = [];
        $this->cablesById = [];
        $this->fibersByCableBoxIdentification = [];
    }

    /**
     * Reinicia os dados usados durante cada execução.
     */
    private function resetRuntime(): void
    {
        $this->processedPower = [];
        $this->propagationCount = 0;
    }
}
