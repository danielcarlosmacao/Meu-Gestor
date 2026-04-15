<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FtthCableFiberBox extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'info',
        'color',
        'number_fiber',
        'input_fiber_box_id',
        'output_fiber_box_id'
    ];

    public function fibers()
    {
        return $this->hasMany(FtthFiberCable::class, 'cable_fiber_box_id');
    }
    public function outputFiberBox()
    {
        return $this->belongsTo(FtthFiberBox::class, 'output_fiber_box_id');
    }

    public function inputFiberBox()
    {
        return $this->belongsTo(FtthFiberBox::class, 'input_fiber_box_id');
    }
}