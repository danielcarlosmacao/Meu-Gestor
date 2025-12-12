<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'license_plate', 'brand', 'model', 'type', 'year', 'fuel_type', 'status', 'color',
    ];

    public function maintenances()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    
}