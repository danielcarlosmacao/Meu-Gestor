<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DatabaseController extends Controller
{
    public function export()
    {
        $filename = 'backup-' . now()->format('Ymd_His') . '.sql';
        $filepath = storage_path("app/" . $filename);

        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $command = '';

        if ($isWindows) {
            $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe'; // ajuste se necessário

            if ($dbPass) {
                // Se tiver senha, usa MYSQL_PWD para evitar travamento
                $command = "set MYSQL_PWD={$dbPass} && \"{$mysqldump}\" -u {$dbUser} -h {$dbHost} {$dbName} > \"{$filepath}\"";
            } else {
                // Sem senha, não usa -p nem MYSQL_PWD
                $command = "\"{$mysqldump}\" -u {$dbUser} -h {$dbHost} {$dbName} > \"{$filepath}\"";
            }
        } else {
            // Linux/macOS
            if ($dbPass) {
                $command = "MYSQL_PWD='{$dbPass}' mysqldump -u {$dbUser} -h {$dbHost} {$dbName} > '{$filepath}'";
            } else {
                $command = "mysqldump -u {$dbUser} -h {$dbHost} {$dbName} > '{$filepath}'";
            }
        }

        $result = null;
        $output = null;
        exec($command, $output, $result);

        \Log::debug('Export command:', ['cmd' => $command]);
        \Log::debug('Export result:', ['result' => $result]);
        \Log::debug('Export output:', $output);

        if ($result === 0 && file_exists($filepath)) {
            return response()->download($filepath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Erro ao exportar o banco de dados.');
    }


    public function import(Request $request)
    {
        \Log::info('Iniciando importação...');

        $request->validate([
            'sql_file' => 'required|file',
            'password' => 'required|string',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->with('error', 'Senha incorreta. Importação cancelada.');
        }
        $file = $request->file('sql_file');

        // Validação da extensão do arquivo
        if ($file->getClientOriginalExtension() !== 'sql') {
            return back()->with('error', 'O arquivo deve ter extensão .sql');
        }

        $filePath = $file->getRealPath();

        // Dados do banco de dados
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbHost = env('DB_HOST', '127.0.0.1');

        // Detecta SO e define comando apropriado
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $mysqlBinary = $isWindows
            ? 'C:\\xampp\\mysql\\bin\\mysql.exe' // Altere para seu caminho
            : 'mysql'; // Linux (assume que está no PATH)

        $command = "\"$mysqlBinary\" -h$dbHost -u$dbUser" .
            ($dbPass ? " -p\"$dbPass\"" : '') .
            " $dbName < \"$filePath\"";

        \Log::info('Comando para importação:', ['cmd' => $command]);

        // Executa comando shell
        $output = null;
        $result = null;
        exec($command . ' 2>&1', $output, $result);

        if ($result === 0) {
            return back()->with('success', 'Banco de dados importado com sucesso via exec()!');
        }

        // Se exec() falhar, tenta via DB::unprepared()
        try {
            \Log::warning('Import via exec() falhou, tentando via DB::unprepared()...');
            $sql = file_get_contents($filePath);
            DB::unprepared($sql);
            return back()->with('success', 'Banco importado via Laravel!');
        } catch (\Exception $e) {
            \Log::error('Erro na importação', ['erro' => $e->getMessage()]);
            return back()->with('error', 'Erro ao importar banco: ' . $e->getMessage());
        }
    }

    public function updateSystem()
    {
        if (!app()->environment('production')) {
            Log::warning('Tentativa de update bloqueada fora do ambiente de produção.');

            return back()->with(
                'error',
                'Atualização não permitida fora do ambiente de produção.'
            );
        }

        try {
            $projectRoot = base_path();

            /*
         * Busca somente a branch cadastrada no banco.
         * Caso não exista ou esteja vazia, utiliza main.
         */
            $branch = trim((string) \App\Models\Option::query()
                ->where('reference', 'branch')
                ->value('value'));

            if (
                $branch === ''
                || strtolower($branch) === 'null'
            ) {
                $branch = 'main';
            }

            /*
         * Valida o nome da branch antes de utilizar no comando.
         */
            if (
                str_starts_with($branch, '-')
                || str_contains($branch, '..')
                || str_contains($branch, '@{')
                || str_ends_with($branch, '.')
                || str_ends_with($branch, '/')
                || str_ends_with($branch, '.lock')
                || !preg_match('/^[A-Za-z0-9._\/-]+$/', $branch)
            ) {
                Log::warning('Branch inválida configurada para atualização.', [
                    'branch' => $branch,
                ]);

                return back()->with(
                    'error',
                    'A branch configurada para atualização é inválida.'
                );
            }

            /*
         * Protege os valores utilizados nos comandos.
         */
            $escapedProjectRoot = escapeshellarg($projectRoot);
            $escapedBranch = escapeshellarg($branch);

            Log::info('Iniciando update via Git', [
                'branch' => $branch,
            ]);

            // Define o diretório como seguro
            exec(
                "git config --global --add safe.directory {$escapedProjectRoot}"
            );

            exec(
                "cd {$escapedProjectRoot} && sudo chown -R $(whoami):$(whoami) .git"
            );

            exec(
                "cd {$escapedProjectRoot} && chmod -R u+rwX .git"
            );

            Log::info('Configurações do diretório .git aplicadas');

            // Salva alterações locais, caso existam
            exec(
                "cd {$escapedProjectRoot} && git stash"
            );

            Log::info('Alterações locais stashed');

            // Garante que o .env local será ignorado
            exec(
                "cd {$escapedProjectRoot} && git update-index --skip-worktree .env"
            );

            Log::info('.env marcado como skip-worktree');

            /*
         * Verifica se a branch existe no repositório origin.
         */
            $branchCheckOutput = [];
            $branchCheckResult = 0;

            exec(
                "cd {$escapedProjectRoot} && git ls-remote --exit-code --heads origin {$escapedBranch} 2>&1",
                $branchCheckOutput,
                $branchCheckResult
            );

            if ($branchCheckResult !== 0) {
                Log::error('Branch não encontrada no repositório remoto.', [
                    'branch' => $branch,
                    'output' => $branchCheckOutput,
                ]);

                return back()->with(
                    'error',
                    "A branch '{$branch}' não existe no repositório origin."
                );
            }

            /*
         * Busca as alterações do repositório.
         */
            $fetchOutput = [];
            $fetchResult = 0;

            exec(
                "cd {$escapedProjectRoot} && git fetch origin {$escapedBranch} 2>&1",
                $fetchOutput,
                $fetchResult
            );

            Log::info('Git fetch output:', $fetchOutput);

            if ($fetchResult !== 0) {
                $errorMessage = implode("\n", $fetchOutput);

                Log::error('Erro no git fetch', [
                    'branch' => $branch,
                    'output' => $fetchOutput,
                ]);

                return back()->with(
                    'error',
                    "Erro ao buscar a branch '{$branch}':\n{$errorMessage}"
                );
            }

            /*
         * Troca para a branch configurada.
         *
         * Caso ela não exista localmente, cria a partir de origin/branch.
         */
            $localBranchExists = 0;

            exec(
                "cd {$escapedProjectRoot} && git show-ref --verify --quiet refs/heads/{$escapedBranch}",
                $localBranchOutput,
                $localBranchExists
            );

            $checkoutOutput = [];
            $checkoutResult = 0;

            if ($localBranchExists === 0) {
                exec(
                    "cd {$escapedProjectRoot} && git checkout {$escapedBranch} 2>&1",
                    $checkoutOutput,
                    $checkoutResult
                );
            } else {
                exec(
                    "cd {$escapedProjectRoot} && git checkout -b {$escapedBranch} origin/{$escapedBranch} 2>&1",
                    $checkoutOutput,
                    $checkoutResult
                );
            }

            Log::info('Git checkout output:', $checkoutOutput);

            if ($checkoutResult !== 0) {
                $errorMessage = implode("\n", $checkoutOutput);

                Log::error('Erro ao trocar de branch', [
                    'branch' => $branch,
                    'output' => $checkoutOutput,
                ]);

                return back()->with(
                    'error',
                    "Erro ao acessar a branch '{$branch}':\n{$errorMessage}"
                );
            }

            // Atualiza usando a branch configurada
            $pullOutput = [];
            $result = 0;

            exec(
                "cd {$escapedProjectRoot} && git pull origin {$escapedBranch} 2>&1",
                $pullOutput,
                $result
            );

            Log::info('Git pull output:', $pullOutput);

            if ($result !== 0) {
                $errorMessage = implode("\n", $pullOutput);

                Log::error('Erro no git pull', [
                    'branch' => $branch,
                    'output' => $pullOutput,
                ]);

                return back()->with(
                    'error',
                    "Erro ao atualizar via Git:\n{$errorMessage}"
                );
            }

            // Checa se o link de storage já existe antes de criar
            $storageLink = public_path('storage');

            if (!is_link($storageLink)) {
                Log::info(
                    'Link simbólico storage/storage não encontrado, criando...'
                );

                Artisan::call('storage:link');

                Log::info(
                    'Link simbólico storage/storage criado com sucesso'
                );
            } else {
                Log::info(
                    'Link simbólico storage/storage já existe, pulando criação'
                );
            }

            /*
         * Executa o Composer dentro do diretório do projeto.
         */
            $composerOutput = [];
            $composerResult = 0;

            exec(
                "cd {$escapedProjectRoot} && composer install --no-dev --optimize-autoloader 2>&1",
                $composerOutput,
                $composerResult
            );

            Log::info('Composer install output:', $composerOutput);

            if ($composerResult !== 0) {
                $errorMessage = implode("\n", $composerOutput);

                Log::error('Erro no composer install', [
                    'output' => $composerOutput,
                ]);

                return back()->with(
                    'error',
                    "Erro ao instalar dependências:\n{$errorMessage}"
                );
            }

            // Roda migrations
            Artisan::call('migrate', [
                '--force' => true,
            ]);

            Log::info('Migrations rodadas com sucesso');

            // Executa o Hook
            try {
                \App\Support\SystemUpdateHook::run();
            } catch (\Throwable $hookException) {
                Log::error('Erro ao executar SystemUpdateHook', [
                    'erro' => $hookException->getMessage(),
                ]);

                // Não interrompe o update se o hook falhar
            }

            Artisan::call('optimize:clear');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            Log::info('Caches limpos');

            return back()->with(
                'success',
                "Sistema atualizado com sucesso pela branch '{$branch}'!"
            );
        } catch (\Throwable $e) {
            Log::error('Erro ao atualizar sistema', [
                'erro' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
            ]);

            return back()->with(
                'error',
                'Erro ao atualizar: ' . $e->getMessage()
            );
        }
    }
}
