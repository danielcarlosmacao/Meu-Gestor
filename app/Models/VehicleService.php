<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleService extends Model
{
      use SoftDeletes;

    protected $fillable = ['vehicle_type', 'name'];

    public function maintenances()
    {
        return $this->belongsToMany(VehicleMaintenance::class, 'vehicle_maintenance_vehicle_service')
                    ->withTimestamps()
                    ->withTrashed();
    }
}
