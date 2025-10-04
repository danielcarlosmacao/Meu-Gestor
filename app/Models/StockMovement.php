<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',          // 'input' ou 'output'
        'description',
        'extra_items',
        'user_id',
    ];

    // Relação com os itens do estoque
    public function items()
    {
        return $this->belongsToMany(StockItem::class, 'stock_movement_items')
                    ->withPivot('quantity','price')
                    ->withTimestamps();
    }

    // Relação com o usuário que fez a movimentação
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}