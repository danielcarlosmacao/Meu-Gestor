<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use App\Services\SettingService;

class ActivityLogController extends Controller
{
    public function index(SettingService $settingService)
    {
        $perPage = $settingService->getPerPage();
        
        $logs = Activity::with('causer')->latest()->paginate($perPage);
        return view('admin.activitylogs.index', compact('logs'));
    }
}
