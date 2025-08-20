<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Services\SettingService;


class ActivityLogController extends Controller
{
    public function index(SettingService $settingService,Request $request)
    {
        $perPage = $settingService->getPerPage();

        $full = $request->query('all') == "s";
        
        $logs = Activity::with('causer')->latest()->paginate($perPage);

        return view('admin.activitylogs.index', compact('logs', 'full'));
    }
}
