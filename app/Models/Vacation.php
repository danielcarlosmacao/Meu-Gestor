<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'collaborator_id',
        'start_date',
        'end_date',
        'information',
    ];
 

    public function collaborator()
    {
        return $this->belongsTo(Collaborator::class);
    }

    public function whatsappLogs()
    {
        return $this->morphMany(WhatsappLog::class, 'ref');
    }

}
