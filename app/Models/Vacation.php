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
    protected $casts = [
    'start_date' => 'date',
    'end_date' => 'date',
];


    public function collaborator()
    {
        return $this->belongsTo(Collaborator::class);
    }

}
