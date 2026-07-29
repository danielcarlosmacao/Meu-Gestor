<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FtthCableRoutePoint extends Model
{
    protected $table = 'ftth_cable_route_points';

    protected $fillable = [
        'cable_fiber_box_id',
        'latitude',
        'longitude',
        'position',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'position' => 'integer',
    ];

    public function cable()
    {
        return $this->belongsTo(
            FtthCableFiberBox::class,
            'cable_fiber_box_id'
        );
    }
}