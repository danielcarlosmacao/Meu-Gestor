<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    protected $fillable = [
        'recipient_id',
        'ref_type',
        'ref_id',
        'status',
        'message',
        'response',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function recipient()
    {
        return $this->belongsTo(Recipient::class);
    }

    /**
     * Relacionamento polimórfico
     * Pode ser Maintenance, Vacation, etc.
     */
    public function ref()
    {
        return $this->morphTo();
    }
}
