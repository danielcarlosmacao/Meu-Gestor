<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FtthFiberBox extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'info',
        'coordinates',
        'pon_id'
    ];

    public function pon()
    {
        return $this->belongsTo(FtthPon::class);
    }

    public function cablesInput()
    {
        return $this->hasMany(FtthCableFiberBox::class,'input_fiber_box_id');
    }

    public function cablesOutput()
    {
        return $this->hasMany(FtthCableFiberBox::class,'output_fiber_box_id');
    }

    public function splinters()
    {
        return $this->hasMany(FtthSplinter::class,'fiber_box_id');
    }
}