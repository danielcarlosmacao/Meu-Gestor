<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reference extends Model
{
    use SoftDeletes;
    public function recipients()
    {
        return $this->belongsToMany(Recipient::class, 'recipient_reference');
    }

}
