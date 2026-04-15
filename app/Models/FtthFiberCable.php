<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FtthFiberCable extends Model
{

    use SoftDeletes;

    protected $table = 'ftth_fiber_cables';

    protected $fillable = [
        'info',
        'fiber_identification',
        'fiber_box_id',
        'optical_power',
        'cable_fiber_box_id',
        'splinter_id',
        'cable_fiber_box_direction',
        'status'
    ];


    public function box()
    {
        return $this->belongsTo(
            FtthFiberBox::class,
            'fiber_box_id'
        );
    }


    public function cable()
    {
        return $this->belongsTo(
            FtthCableFiberBox::class,
            'cable_fiber_box_id'
        );
    }


    public function splinter()
    {
        return $this->belongsTo(
            FtthSplinter::class,
            'splinter_id'
        );
    }

    public function fusions1()
    {
        return $this->hasMany(FtthFiberFusion::class, 'fiber_cables_id_1');
    }

    public function fusions2()
    {
        return $this->hasMany(FtthFiberFusion::class, 'fiber_cables_id_2');
    }

}