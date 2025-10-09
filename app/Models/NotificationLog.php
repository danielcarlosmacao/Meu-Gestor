<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class NotificationLog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'notification_id',
        'recipient_id',
        'status',
        'message',
        'response',
        'sent_at',
    ];
    protected $casts = ['sent_at' => 'datetime',];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }

    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }
}
