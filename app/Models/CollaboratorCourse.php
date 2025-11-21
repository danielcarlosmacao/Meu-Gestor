<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CollaboratorCourse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'collaborator_id',
        'title',
        'description',
        'valid_until',
        'file_path',
        'token',
    ];

    public function getRouteKeyName()
    {
        return 'token';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->token) {
                $model->token = bin2hex(random_bytes(20));
            }
        });
    }

    public function collaborator()
    {
        return $this->belongsTo(Collaborator::class);
    }
}
