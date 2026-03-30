<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TowerGallery extends Model
{
     use SoftDeletes;

    protected $table = 'tower_gallery';

    protected $fillable = [
        'tower_id',
        'path',
        'title'
    ];
}
