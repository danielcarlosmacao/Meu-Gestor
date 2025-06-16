<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TowerSummaries extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tower_id',
        'consumption_ah_day',
        'time_ah_consumption',
        'battery_required',
        'watts_plate',
        'amps_plate',
    ];

    
    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }
}
