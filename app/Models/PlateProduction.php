<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlateProduction extends Model
{
    use SoftDeletes;

    protected $fillable = ['tower_id', 'plate_id', 'installation_date'];

    protected $casts = [
        'installation_date' => 'date',
    ];

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }

    public function plate()
    {
        return $this->belongsTo(Plate::class);
    }
}
