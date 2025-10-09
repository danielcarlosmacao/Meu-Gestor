<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Battery extends Model
{
    use softDeletes;
    
    protected $table = 'batterys';

    protected $fillable = ['name', 'mark', 'amps'];

    public function batteryProductions()
    {
        return $this->hasMany(BatteryProduction::class);
    }
}
