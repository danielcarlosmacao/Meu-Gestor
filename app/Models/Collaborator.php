<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact',
        'admission_date',
        'user_id',
        'job_title_id',
        'work_scale',
        'color',
        'status',
    ];
    public function ferias()
{
    return $this->hasMany(Vacation::class);
}

public function courses()
{
    return $this->hasMany(CollaboratorCourse::class);
}


    public function jobTitle()
    {
        return $this->belongsTo(CollaboratorJobTitle::class, 'job_title_id');
    }

    public function workSchedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
