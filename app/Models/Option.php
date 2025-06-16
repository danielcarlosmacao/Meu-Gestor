<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
        use SoftDeletes;

    protected $fillable = ['reference', 'value'];

}
