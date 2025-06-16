<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class collaborator extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'admission_date',
        'color',
        'status',
    ];
    public function ferias()
{
    return $this->hasMany(Vacation::class);
}
}
