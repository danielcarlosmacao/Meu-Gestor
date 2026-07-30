@extends('layouts.header')

@section('title', 'FTTH - BOX')

@section('content')

    @php
        /*
        |--------------------------------------------------------------------------
        | CORES DAS FIBRAS
        |--------------------------------------------------------------------------
        */

        $fiberColors = [
            1 => '#00a651', // Verde
            2 => '#ffff00', // Amarelo
            3 => '#ffffff', // Branco
            4 => '#0000ff', // Azul
            5 => '#ff0000', // Vermelho
            6 => '#8A2BE2', // Violeta
            7 => '#8b4513', // Marrom
            8 => '#ffc0cb', // Rosa
            9 => '#000000', // Preto
            10 => '#808080', // Cinza
            11 => '#ffa500', // Laranja
            12 => '#00ffff', // Água

            13 => '#00a651', // Verde
            14 => '#ffff00', // Amarelo
            15 => '#ffffff', // Branco
            16 => '#0000ff', // Azul
            17 => '#ff0000', // Vermelho
            18 => '#8A2BE2', // Violeta
            19 => '#8b4513', // Marrom
            20 => '#ffc0cb', // Rosa
            21 => '#000000', // Preto
            22 => '#808080', // Cinza
            23 => '#ffa500', // Laranja
            24 => '#00ffff', // Água
        ];

        /*
        |--------------------------------------------------------------------------
        | FUNÇÕES AUXILIARES
        |--------------------------------------------------------------------------
        |
        | Foram utilizadas closures para evitar erro de função já declarada.
        |
        */

        $getFiberNumber = function ($name) {
            if (preg_match('/(\d+)$/', (string) $name, $matches)) {
                return (int) $matches[1];
            }

            return null;
        };

        $getTextColor = function ($background) {
            $darkColors = ['#000000', '#0000ff', '#8b4513', '#8a2be2', '#808080'];

            return in_array(strtolower((string) $background), $darkColors) ? '#ffffff' : '#000000';
        };

        $getStatusClass = function ($status) {
            return match ($status) {
                'unused' => 'secondary',
                'used' => 'success',
                'fusion' => 'primary',
                'fused' => 'primary',
                'splinter' => 'warning',
                'active' => 'success',
                'inactive' => 'danger',
                default => 'secondary',
            };
        };

        $getSignalClass = function ($signal) {
            if ($signal === null || $signal === '') {
                return 'secondary';
            }

            $value = (float) $signal;

            return match (true) {
                $value >= -15 => 'success',
                $value >= -23 => 'warning',
                default => 'danger',
            };
        };
    @endphp

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/ftthbox.css') }}">
    @endpush


    <div class="container-fluid ftth-page">

        {{-- CABEÇALHO DA BOX --}}
        <div class="card ftth-header-card shadow-sm mb-3 mt-2 mt-md-4">

            <div class="card-body">

                <div
                    class="d-flex flex-column flex-lg-row
                            justify-content-between align-items-lg-center gap-3">

                    <div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            <div class="rounded-circle bg-primary-subtle text-primary
                                        d-flex align-items-center justify-content-center"
                                style="width: 45px; height: 45px;">

                                <i class="bi bi-box-seam fs-5"></i>
                            </div>

                            <div>
                                <h2 class="fw-bold mb-1">
                                    {{ $box->info ?: 'Caixa sem descrição' }}
                                </h2>

                                <div class="d-flex flex-wrap gap-2 text-muted small">

                                    <span>
                                        <i class="bi bi-hash"></i>
                                        Caixa {{ $box->number }}
                                    </span>

                                    @if ($box->pon)
                                        <span>
                                            <i class="bi bi-diagram-3"></i>
                                            {{ $box->pon->info }}
                                        </span>
                                    @endif

                                    @if ($box->coordinates)
                                        <span>
                                            <i class="bi bi-geo-alt"></i>
                                            {{ $box->coordinates }}
                                        </span>
                                    @endif

                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">

                        <a href="{{ route('fiberbox.index', [
                            'pon' => $box->pon_id,
                            'map' => 'yes',
                        ]) }}"
                            class="btn dcm-btn-primary" title="Voltar ao mapa" aria-label="Voltar ao mapa">

                            <i class="bi bi-map me-1"></i>
                            <span class="d-none d-sm-inline">Mapa</span>
                        </a>

                        @can('ftth.update')
                            <button type="button" class="btn btn-outline-warning"
                                onclick="openConfirmModal(
                                    '{{ route('fiberbox.recalculate.local', $box->id) }}',
                                    'Deseja recalcular toda a rede desta CTO?',
                                    'Essa alteração afeta esta caixa e todas as caixas conectadas a ela.',
                                    'POST'
                                )"
                                title="Recalcular sinais da rede" aria-label="Recalcular sinais da rede">

                                <i class="bi bi-arrow-repeat me-1"></i>
                                <span class="d-none d-sm-inline">Recalcular</span>
                            </button>
                        @endcan

                    </div>

                </div>

            </div>

        </div>

        {{-- RESUMO --}}
        <div class="row g-2 mb-3">

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-circle bg-primary-subtle text-primary
                                        d-flex align-items-center justify-content-center"
                                style="width: 42px; height: 42px;">

                                <i class="bi bi-bezier2"></i>
                            </div>

                            <div>
                                <div class="small text-muted">Cabos</div>
                                <div class="fw-bold fs-5">{{ $cables->count() }}</div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-circle bg-success-subtle text-success
                                        d-flex align-items-center justify-content-center"
                                style="width: 42px; height: 42px;">

                                <i class="bi bi-list-ul"></i>
                            </div>

                            <div>
                                <div class="small text-muted">Fibras</div>
                                <div class="fw-bold fs-5">{{ $fibers->count() }}</div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-circle bg-warning-subtle text-warning
                                        d-flex align-items-center justify-content-center"
                                style="width: 42px; height: 42px;">

                                <i class="bi bi-diagram-3"></i>
                            </div>

                            <div>
                                <div class="small text-muted">Splitters</div>
                                <div class="fw-bold fs-5">{{ $splinters->count() }}</div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3">

                        <div class="d-flex align-items-center gap-3">

                            <div class="rounded-circle bg-info-subtle text-info
                                        d-flex align-items-center justify-content-center"
                                style="width: 42px; height: 42px;">

                                <i class="bi bi-link-45deg"></i>
                            </div>

                            <div>
                                <div class="small text-muted">Fusões</div>
                                <div class="fw-bold fs-5">{{ $fusions->count() }}</div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

        <div class="row g-3">

            {{-- ================================================================
                COLUNA ESQUERDA — CABOS
            ================================================================= --}}

            <div class="col-12 col-xl-3 ftth-column">

                <div class="card ftth-card shadow-sm mb-3">

                    <div
                        class="card-header bgc-primary text-white
                                d-flex justify-content-between align-items-center">

                        <div class="fw-bold">
                            <i class="bi bi-bezier2 me-1"></i>
                            Cabos

                            <span class="badge bg-light text-dark ms-1">
                                {{ $cables->count() }}
                            </span>
                        </div>

                        @can('ftth.create')
                            <button type="button" class="btn btn-sm btn-light ftth-action-button" data-bs-toggle="modal"
                                data-bs-target="#modalCable" title="Cadastrar cabo" aria-label="Cadastrar cabo">

                                <i class="bi bi-plus-lg"></i>
                            </button>
                        @endcan

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover table-sm align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Descrição</th>
                                        <th>Conexão</th>
                                        <th width="55" class="text-end pe-3">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($cables as $cable)

                                        <tr style="border-left: 5px solid {{ $cable->color ?: '#6c757d' }}">

                                            {{-- DESCRIÇÃO --}}
                                            <td class="ps-3">

                                                <div class="d-flex align-items-center gap-2">

                                                    <span class="cable-color-indicator"
                                                        style="background-color: {{ $cable->color ?: '#6c757d' }}"
                                                        onclick="copyColor('{{ $cable->color ?: '#6c757d' }}')"
                                                        title="Copiar cor {{ $cable->color }}">
                                                    </span>

                                                    <div>

                                                        <div class="fw-semibold">
                                                            {{ $cable->info ?: 'Cabo sem descrição' }}
                                                        </div>

                                                        @if ($cable->number_fiber)
                                                            <div class="small text-muted">
                                                                {{ $cable->number_fiber }} fibras
                                                            </div>
                                                        @endif

                                                    </div>

                                                </div>

                                            </td>

                                            {{-- CONEXÃO --}}
                                            <td>

                                                @if ($cable->input_fiber_box_id == $box->id)
                                                    @if ($cable->output_fiber_box_id)
                                                        <a href="{{ route('fiberbox.show', $cable->output_fiber_box_id) }}"
                                                            class="text-decoration-none"
                                                            title="{{ $cable->outputFiberBox->info ?? 'Abrir caixa' }}">

                                                            <span class="badge bg-secondary connection-badge">
                                                                <i class="bi bi-box-arrow-right me-1"></i>
                                                                {{ $cable->outputFiberBox->info ?? 'Caixa não encontrada' }}
                                                            </span>
                                                        </a>
                                                    @else
                                                        <span class="badge bg-light text-muted border">
                                                            <i class="bi bi-dash-circle me-1"></i>
                                                            Sem destino
                                                        </span>
                                                    @endif
                                                @else
                                                    @if ($cable->input_fiber_box_id)
                                                        <a href="{{ route('fiberbox.show', $cable->input_fiber_box_id) }}"
                                                            class="text-decoration-none"
                                                            title="{{ $cable->inputFiberBox->info ?? 'Abrir caixa' }}">

                                                            <span class="badge bg-secondary connection-badge">
                                                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                                                {{ $cable->inputFiberBox->info ?? 'Caixa não encontrada' }}
                                                            </span>
                                                        </a>
                                                    @else
                                                        <span class="badge bg-light text-muted border">
                                                            <i class="bi bi-dash-circle me-1"></i>
                                                            Sem origem
                                                        </span>
                                                    @endif
                                                @endif

                                            </td>

                                            {{-- AÇÕES --}}
                                            <td class="text-end pe-3">

                                                @can('ftth.delete')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger ftth-action-button"
                                                        onclick="openConfirmModal(
                                                            '{{ route('cable.destroy', $cable->id) }}',
                                                            'Tem certeza que deseja excluir este cabo?',
                                                            'Essa alteração não poderá ser revertida.',
                                                            'DELETE'
                                                        )"
                                                        title="Excluir cabo" aria-label="Excluir cabo">

                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endcan

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="3" class="empty-state text-center text-muted">

                                                <i class="bi bi-bezier2 d-block mb-2"></i>

                                                <div class="fw-semibold">
                                                    Nenhum cabo cadastrado
                                                </div>

                                                <div class="small">
                                                    Use o botão + para cadastrar.
                                                </div>

                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            {{-- ================================================================
                COLUNA CENTRAL — FIBRAS
            ================================================================= --}}

            <div class="col-12 col-xl-5 ftth-column">

                <div class="card ftth-card shadow-sm mb-3">

                    <div
                        class="card-header bgc-primary text-white
                                d-flex justify-content-between align-items-center">

                        <div class="fw-bold">
                            <i class="bi bi-list-ul me-1"></i>
                            Bandeja de fibras

                            <span class="badge bg-light text-dark ms-1">
                                {{ $fibers->count() }}
                            </span>
                        </div>

                        @can('ftth.create')
                            <button type="button" class="btn btn-sm btn-light ftth-action-button" data-bs-toggle="modal"
                                data-bs-target="#modalFiber" title="Cadastrar fibras" aria-label="Cadastrar fibras">

                                <i class="bi bi-plus-lg"></i>
                            </button>
                        @endcan

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-sm table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Fibra</th>
                                        <th>Conexões</th>
                                        <th>Status</th>
                                        <th>Sinal</th>
                                        <th width="85" class="text-end pe-3">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($fibers as $fiber)

                                        @php
                                            $fiberNumber = $getFiberNumber($fiber->fiber_identification);
                                            $fiberBackground = $fiberColors[$fiberNumber] ?? '#f8f9fa';
                                            $fiberTextColor = $getTextColor($fiberBackground);

                                            $statusClass = $getStatusClass($fiber->status);
                                            $signalClass = $getSignalClass($fiber->optical_power);
                                        @endphp

                                        <tr class="fiber-row">

                                            {{-- FIBRA --}}
                                            <td class="fiber-color-cell ps-3"
                                                style="
                                                    background: {{ $fiberBackground }};
                                                    color: {{ $fiberTextColor }};
                                                    font-weight: 700;
                                                ">

                                                <div class="d-flex align-items-center gap-2">

                                                    <span>
                                                        {{ $fiber->fiber_identification }}
                                                    </span>

                                                </div>

                                            </td>

                                            {{-- FUSÃO / SPLITTER --}}
                                            <td class="p-1">

                                                @if ($fiber->splinter)
                                                    <div class="rounded border bg-light p-2 mb-1 small">

                                                        <div class="fw-semibold">

                                                            <i class="bi bi-diagram-3 text-warning me-1"></i>

                                                            {{ $fiber->splinter->name }}

                                                        </div>

                                                        <div class="text-muted mt-1">

                                                            {{ $fiber->splinter->loss->type ?? 'Tipo não informado' }}

                                                        </div>

                                                    </div>
                                                @endif

                                                @foreach ($fiber->fusions1 as $fusion)
                                                    @php
                                                        $fusionFiberName =
                                                            $fusion->fiber2->fiber_identification ?? null;

                                                        $fusionNumber = $getFiberNumber($fusionFiberName);

                                                        $fusionBackground = $fiberColors[$fusionNumber] ?? '#f8f9fa';

                                                        $fusionTextColor = $getTextColor($fusionBackground);
                                                    @endphp

                                                    <div class="fusion-color-item px-2 py-2 mb-1"
                                                        style="
                                                            background: {{ $fusionBackground }};
                                                            color: {{ $fusionTextColor }};
                                                            border-left: 4px solid rgba(0, 0, 0, 0.2);
                                                        ">

                                                        <i class="bi bi-link-45deg me-1"></i>

                                                        {{ $fusionFiberName ?? 'Fibra não encontrada' }}

                                                    </div>
                                                @endforeach

                                                @foreach ($fiber->fusions2 ?? [] as $fusion)
                                                    @php
                                                        $fusionFiberName =
                                                            $fusion->fiber1->fiber_identification ?? null;

                                                        $fusionNumber = $getFiberNumber($fusionFiberName);

                                                        $fusionBackground = $fiberColors[$fusionNumber] ?? '#f8f9fa';

                                                        $fusionTextColor = $getTextColor($fusionBackground);
                                                    @endphp

                                                    <div class="fusion-color-item px-2 py-2 mb-1"
                                                        style="
                                                            background: {{ $fusionBackground }};
                                                            color: {{ $fusionTextColor }};
                                                            border-left: 4px solid rgba(0, 0, 0, 0.2);
                                                        ">

                                                        <i class="bi bi-link-45deg me-1"></i>

                                                        {{ $fusionFiberName ?? 'Fibra não encontrada' }}

                                                    </div>
                                                @endforeach

                                                @if (!$fiber->splinter && $fiber->fusions1->isEmpty() && (!isset($fiber->fusions2) || $fiber->fusions2->isEmpty()))
                                                    <span class="small text-muted ps-2">
                                                        Sem conexão
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- STATUS --}}
                                            <td>

                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ __('status.' . $fiber->status) }}
                                                </span>

                                            </td>

                                            {{-- SINAL --}}
                                            <td>

                                                @if ($fiber->optical_power !== null && $fiber->optical_power !== '')
                                                    <span
                                                        class="badge signal-badge
                                                        bg-{{ $signalClass }}-subtle
                                                        text-{{ $signalClass }}
                                                        border border-{{ $signalClass }}-subtle">

                                                        <i class="bi bi-reception-4 me-1"></i>

                                                        {{ number_format((float) $fiber->optical_power, 2, ',', '.') }}
                                                        dBm

                                                    </span>
                                                @else
                                                    <span
                                                        class="badge signal-badge
                                                        bg-secondary-subtle text-secondary border">

                                                        Sem sinal
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- AÇÕES --}}
                                            <td class="text-end pe-3">

                                                <div class="d-inline-flex justify-content-end gap-1">

                                                    @if ($fiber->status === 'unused')
                                                        @can('ftth.delete')
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-danger ftth-action-button"
                                                                onclick="openConfirmModal(
                                                                    '{{ route('fiber.destroy', $fiber->id) }}',
                                                                    'Tem certeza que deseja excluir esta fibra?',
                                                                    'Essa alteração não poderá ser revertida.',
                                                                    'DELETE'
                                                                )"
                                                                title="Excluir fibra" aria-label="Excluir fibra">

                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endcan
                                                    @endif

                                                    @can('ftth.update')
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-warning
                                                                ftth-action-button btn-edit-signal"
                                                            data-id="{{ $fiber->id }}"
                                                            data-fiber="{{ $fiber->fiber_identification }}"
                                                            data-signal="{{ $fiber->optical_power }}" title="Editar sinal"
                                                            aria-label="Editar sinal">

                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    @endcan

                                                </div>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="5" class="empty-state text-center text-muted">

                                                <i class="bi bi-list-ul d-block mb-2"></i>

                                                <div class="fw-semibold">
                                                    Nenhuma fibra cadastrada
                                                </div>

                                                <div class="small">
                                                    Use o botão + para adicionar as fibras.
                                                </div>

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            {{-- ================================================================
                COLUNA DIREITA — SPLITTERS E FUSÕES
            ================================================================= --}}

            <div class="col-12 col-xl-4 ftth-column">

                {{-- SPLITTERS --}}
                <div class="card ftth-card shadow-sm mb-3">

                    <div
                        class="card-header bgc-primary text-white
                                d-flex justify-content-between align-items-center">

                        <div class="fw-bold">
                            <i class="bi bi-diagram-3 me-1"></i>
                            Splitters

                            <span class="badge bg-light text-dark ms-1">
                                {{ $splinters->count() }}
                            </span>
                        </div>

                        @can('ftth.create')
                            <button type="button" class="btn btn-sm btn-light ftth-action-button" data-bs-toggle="modal"
                                data-bs-target="#modalSplinter" title="Cadastrar splitter" aria-label="Cadastrar splitter">

                                <i class="bi bi-plus-lg"></i>
                            </button>
                        @endcan

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-hover table-sm align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Nome</th>
                                        <th>Tipo</th>
                                        <th>Fibra</th>
                                        <th>Splitter</th>
                                        <th>Sinal</th>
                                        <th width="55" class="text-end pe-3">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($splinters as $spl)

                                        @php
                                            $inputPower = $spl->inputCable->optical_power ?? null;
                                            $loss1 = $spl->loss->loss1 ?? 0;
                                            $loss2 = $spl->loss->loss2 ?? 0;
                                            $splitterType = $spl->loss->splinter_type ?? null;
                                        @endphp

                                        <tr>

                                            {{-- NOME --}}
                                            <td class="ps-3 fw-semibold">

                                                <i class="bi bi-diagram-3 text-warning me-1"></i>

                                                {{ $spl->name }}

                                            </td>

                                            {{-- TIPO --}}
                                            <td>

                                                <span class="badge bg-secondary">
                                                    {{ __('fiber.' . $spl->type) }}
                                                </span>

                                            </td>

                                            {{-- FIBRA --}}
                                            <td>

                                                @if ($spl->inputCable)
                                                    <small class="fw-semibold">
                                                        {{ $spl->inputCable->fiber_identification }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">
                                                        —
                                                    </small>
                                                @endif

                                            </td>

                                            {{-- LOSS --}}
                                            <td>

                                                <span class="badge bg-dark">
                                                    {{ $spl->loss->type ?? 'Não informado' }}
                                                </span>

                                            </td>

                                            {{-- SINAL --}}
                                            <td class="text-nowrap">

                                                @if ($inputPower !== null)
                                                    <div class="splitter-signal-line small text-muted">

                                                        Entrada:

                                                        <strong class="text-dark">
                                                            {{ number_format((float) $inputPower, 2, ',', '.') }}
                                                            dBm
                                                        </strong>

                                                    </div>

                                                    @if ($splitterType === 'balanced')
                                                        <div class="splitter-signal-line small mt-1">

                                                            Saída:

                                                            <strong>
                                                                {{ number_format((float) $inputPower - (float) $loss1, 2, ',', '.') }}
                                                                dBm
                                                            </strong>

                                                        </div>
                                                    @else
                                                        <div class="splitter-signal-line small mt-1">

                                                            Saída 1:

                                                            <strong>
                                                                {{ number_format((float) $inputPower - (float) $loss1, 2, ',', '.') }}
                                                                dBm
                                                            </strong>

                                                        </div>

                                                        <div class="splitter-signal-line small">

                                                            Saída 2:

                                                            <strong>
                                                                {{ number_format((float) $inputPower - (float) $loss2, 2, ',', '.') }}
                                                                dBm
                                                            </strong>

                                                        </div>
                                                    @endif
                                                @else
                                                    <span
                                                        class="badge bg-secondary-subtle
                                                        text-secondary border">

                                                        Sem sinal
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- AÇÕES --}}
                                            <td class="text-end pe-3">

                                                @can('ftth.delete')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger ftth-action-button"
                                                        onclick="openConfirmModal(
                                                            '{{ route('splinter.destroy', $spl->id) }}',
                                                            'Tem certeza que deseja excluir este splitter?',
                                                            'Essa alteração não poderá ser revertida.',
                                                            'DELETE'
                                                        )"
                                                        title="Excluir splitter" aria-label="Excluir splitter">

                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endcan

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="6" class="empty-state text-center text-muted">

                                                <i class="bi bi-diagram-3 d-block mb-2"></i>

                                                <div class="fw-semibold">
                                                    Nenhum splitter cadastrado
                                                </div>

                                                <div class="small">
                                                    Use o botão + para cadastrar.
                                                </div>

                                            </td>

                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                {{-- FUSÕES --}}
                <div class="card ftth-card shadow-sm mb-3">

                    <div
                        class="card-header bgc-primary text-white
                                d-flex justify-content-between align-items-center">

                        <div class="fw-bold">
                            <i class="bi bi-link-45deg me-1"></i>
                            Fusões

                            <span class="badge bg-light text-dark ms-1">
                                {{ $fusions->count() }}
                            </span>
                        </div>

                        @can('ftth.create')
                            <button type="button" class="btn btn-sm btn-light ftth-action-button" data-bs-toggle="modal"
                                data-bs-target="#modalFusion" title="Cadastrar fusão" aria-label="Cadastrar fusão">

                                <i class="bi bi-plus-lg"></i>
                            </button>
                        @endcan

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-sm table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Fibra 1</th>
                                        <th>Fibra 2</th>
                                        <th width="90" class="text-end pe-3">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse ($fusions as $fusion)
                                        @php
                                            $fiberName1 = $fusion->fiber1->fiber_identification ?? null;

                                            $fiberNumber1 = $getFiberNumber($fiberName1);

                                            $fiberBackground1 = $fiberColors[$fiberNumber1] ?? '#f8f9fa';

                                            $fiberText1 = $getTextColor($fiberBackground1);

                                            $fiberName2 = $fusion->fiber2->fiber_identification ?? null;

                                            $fiberNumber2 = $getFiberNumber($fiberName2);

                                            $fiberBackground2 = $fiberColors[$fiberNumber2] ?? '#f8f9fa';

                                            $fiberText2 = $getTextColor($fiberBackground2);
                                        @endphp

                                        <tr>

                                            {{-- FIBRA 1 --}}
                                            <td class="ps-3"
                                                style="
                                                    background: {{ $fiberBackground1 }};
                                                    color: {{ $fiberText1 }};
                                                    font-weight: 700;
                                                ">

                                                {{ $fiberName1 ?? 'Fibra não encontrada' }}

                                            </td>

                                            {{-- FIBRA 2 --}}
                                            <td
                                                style="
                                                    background: {{ $fiberBackground2 }};
                                                    color: {{ $fiberText2 }};
                                                    font-weight: 700;
                                                ">

                                                {{ $fiberName2 ?? 'Fibra não encontrada' }}

                                            </td>

                                            {{-- AÇÕES --}}
                                            <td class="text-end pe-3">

                                                <div class="d-inline-flex align-items-center gap-1">

                                                    @if ($fusion->info)
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary ftth-action-button"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ $fusion->info }}"
                                                            aria-label="Informações da fusão">

                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    @endif

                                                    @can('ftth.delete')
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-danger ftth-action-button"
                                                            onclick="openConfirmModal(
                                                                '{{ route('fusion.destroy', $fusion->id) }}',
                                                                'Tem certeza que deseja excluir esta fusão?',
                                                                'Essa alteração não poderá ser revertida.',
                                                                'DELETE'
                                                            )"
                                                            title="Excluir fusão" aria-label="Excluir fusão">

                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endcan

                                                </div>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="3" class="empty-state text-center text-muted">

                                                <i class="bi bi-link-45deg d-block mb-2"></i>

                                                <div class="fw-semibold">
                                                    Nenhuma fusão cadastrada
                                                </div>

                                                <div class="small">
                                                    Use o botão + para cadastrar.
                                                </div>

                                            </td>

                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- =====================================================================
        MODAIS
    ====================================================================== --}}

    @include('ftth.modals.cable')
    @include('ftth.modals.fiber')
    @include('ftth.modals.splinter')
    @include('ftth.modals.fusion')
    @include('ftth.modals.editSignal')

    {{-- =====================================================================
        JAVASCRIPT
    ====================================================================== --}}

@push('scripts')
    <script>
        window.ftthBoxData = {
            fibersAll: @json($allFibers),
            currentBoxId: @json($box->id),
            fiberColors: @json($fiberColors),
            updateSignalUrl: @json(url('/ftth/fiber-box/updatesignal')),
            fiberUpdateUrl: @json(route('fiber.update', ':id')),
        };
    </script>

    <script src="{{ asset('js/ftthbox.js') }}"></script>
@endpush
@endsection
