<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tower_id',
        'info',
        'maintenance_date',
        'next_maintenance_date',
        'status',
    ];
     protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }
}
