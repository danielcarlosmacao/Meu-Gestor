<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class CollaboratorJobtitle extends Model
{
    use softDeletes;

   protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function collaborators()
    {
        return $this->hasMany(Collaborator::class, 'job_title_id');
    }
}
