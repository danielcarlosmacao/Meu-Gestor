<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentProduction extends Model
{
    use SoftDeletes;

    protected $fillable = ['identification', 'tower_id', 'equipment_id', 'active'];

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
