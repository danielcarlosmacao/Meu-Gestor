<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
    ]);

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
    try {
        $projectRoot = base_path();

        // Define o diretório como seguro
        exec("git config --global --add safe.directory '$projectRoot'");

        // Salva alterações locais (se houver)
        exec("cd $projectRoot && git stash");

        // Atualiza com Git Pull
        $pullOutput = [];
        $result = 0;
        exec("cd $projectRoot && git pull origin main 2>&1", $pullOutput, $result);
        Log::info('Git pull output:', $pullOutput);

        if ($result !== 0) {
            $errorMessage = implode("\n", $pullOutput);
            return back()->with('error', "Erro ao atualizar via Git:\n" . $errorMessage);
        }

        // Roda migrations e limpa caches
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('optimize:clear');

        return back()->with('success', 'Sistema atualizado com sucesso!');
    } catch (\Exception $e) {
        Log::error('Erro ao atualizar sistema', ['erro' => $e->getMessage()]);
        return back()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
    }
}


}
