<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'min_stock', 'price', 'current_stock','status'];

    // Escopo para ativos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    public function movements()
    {
        return $this->belongsToMany(StockMovement::class, 'stock_movement_items')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }
}
