<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
        use SoftDeletes;

    protected $fillable = ['reference', 'value'];

    public static function getValue(string $reference): ?string
    {
        return static::where('reference', $reference)->value('value');
    }

}
