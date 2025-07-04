<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;
    protected $fillable = ['info', 'msg', 'sent', 'send_at'];
    protected $casts = ['send_at' => 'datetime',];
    

    public function recipients()
    {
        return $this->belongsToMany(Recipient::class, 'notification_recipient');
    }

    public function logs()
{
    return $this->hasMany(NotificationLog::class);
}
}
