<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipient extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'reference', 'number'];

    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_recipient');
    }
    public function references()
    {
        return $this->belongsToMany(Reference::class, 'recipient_reference');
    }

}
