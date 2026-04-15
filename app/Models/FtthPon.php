<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FtthPon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'olt',
        'info',
        'signal'
    ];

    public function boxes()
    {
        return $this->hasMany(FtthFiberBox::class,'pon_id');
    }
}