<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use SoftDeletes;
    
    protected $table = 'equipments'; // Define explicitamente o nome da tabel
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
    protected $fillable = ['name', 'watts'];

    public function equipmentProductions()
    {
        return $this->hasMany(EquipmentProduction::class);
    }
}
