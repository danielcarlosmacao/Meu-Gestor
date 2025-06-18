<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Postit extends Model
{
  
    use HasFactory;

    protected $fillable = [
        'user_id', 'content', 'color', 'pos_x', 'pos_y', 'width', 'height'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
