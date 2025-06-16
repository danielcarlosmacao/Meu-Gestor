<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleMaintenance extends Model
{
   use SoftDeletes;

    protected $fillable = [
        'vehicle_id', 'type', 'maintenance_date', 'cost', 'status', 'mileage','workshop', 'parts_used',
    ];

    protected $dates = ['maintenance_date'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function services()
    {
        return $this->belongsToMany(VehicleService::class, 'vehicle_maintenance_vehicle_service')
                    ->withTimestamps()
                    ->withTrashed(); // opcional, se quiser permitir soft delete nos serviços
    }
}
