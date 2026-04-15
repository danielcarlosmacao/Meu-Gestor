<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FtthFiberFusion extends Model
{
    protected $table = 'ftth_fiber_fusion';

    protected $fillable = [
        'info',
        'fiber_box_id',
        'fiber_cables_id_1',
        'fiber_cables_id_2'
    ];

    public function fiber1()
    {
        return $this->belongsTo(FtthFiberCable::class, 'fiber_cables_id_1');
    }

    public function fiber2()
    {
        return $this->belongsTo(FtthFiberCable::class, 'fiber_cables_id_2');
    }
}