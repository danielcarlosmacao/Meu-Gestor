<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Services\SettingService;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;


class ActivityLogController extends Controller
{
   public function index(SettingService $settingService, Request $request)
{
    $perPage = $settingService->getPerPage();

    $debug = $request->query('debug') === "s";
    $full  = $request->query('full') === "s";

    $logsQuery = Activity::with('causer')->latest();

    // Se não for "full=s", exclui os logs de User
    if (!$full) {
        $logsQuery->where('subject_type', '!=', \App\Models\User::class);
    }

    $logs = $logsQuery->paginate($perPage);

    return view('admin.activitylogs.index', compact('logs', 'debug', 'full'));
}


     public function laravelLog(SettingService $settingService,Request $request)
    {
         $filePath = storage_path('logs/laravel.log');

        if (!File::exists($filePath)) {
            abort(404, 'Log file not found');
        }

        // Lê o arquivo linha por linha
        $lines = File::lines($filePath);
        $lines = iterator_to_array($lines);
        $lines = array_reverse($lines); // mais recentes primeiro

        // Filtrar por nível, se houver
        $level = $request->query('level');
        if ($level) {
            $lines = array_filter($lines, fn($line) => str_contains(strtoupper($line), strtoupper($level)));
        }

        // Paginação
        $perPage = 50;
        $page = $request->query('page', 1);
        $items = array_slice($lines, ($page - 1) * $perPage, $perPage);
        $logs = new LengthAwarePaginator(
            $items,
            count($lines),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.activitylogs.laravel', compact('logs', 'level'));

    }
}
