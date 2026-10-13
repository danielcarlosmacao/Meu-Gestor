<?php

namespace App\Models;
use App\Enums\ScheduleType;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $table = 'work_schedules';

    protected $fillable = [
        'collaborator_id',
        'date',
        'type',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function collaborator()
    {
        return $this->belongsTo(Collaborator::class);
    }
}
