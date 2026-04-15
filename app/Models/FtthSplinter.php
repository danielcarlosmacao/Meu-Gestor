<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FtthSplinter extends Model
{

    use SoftDeletes;

    protected $table = 'ftth_splinters';

    protected $fillable = [
        'name',
        'type',
        'fiber_box_id',
        'splinter_input',
        'splinter'
    ];


    public function box()
    {
        return $this->belongsTo(
            FtthFiberBox::class,
            'fiber_box_id'
        );
    }


    public function inputCable()
    {
        return $this->belongsTo(
            FtthFiberCable::class,
            'splinter_input'
        );
    }


    public function loss()
    {
        return $this->belongsTo(
            FtthSplinterLoss::class,
            'splinter'
        );
    }

}