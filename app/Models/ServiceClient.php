<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceClient extends Model
{
    use SoftDeletes;
      protected $fillable = ['name', 'status'];

    public function equipmentMaintenances()
    {
        return $this->hasMany(ServiceEquipmentMaintenance::class);
    }

    public function maintenances()
    {
        return $this->hasMany(ServiceMaintenance::class);
    }
}
