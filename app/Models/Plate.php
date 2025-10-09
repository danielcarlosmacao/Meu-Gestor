<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Plate extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'amps', 'watts'];

    public function plateProductions()
    {
        return $this->hasMany(PlateProduction::class);
    }
}
