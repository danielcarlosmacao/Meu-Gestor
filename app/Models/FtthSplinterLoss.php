<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FtthSplinterLoss extends Model
{

    use SoftDeletes;

    protected $table = 'ftth_splinter_losses';

    protected $fillable = [
        'type',
        'derivations',
        'splinter_type',
        'loss1',
        'loss2'
    ];

}