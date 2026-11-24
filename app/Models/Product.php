<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'kode_produk',
        'nama_produk',
        'harga_jual',
        'harga_pokok',
        'stok',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'harga_jual' => 'decimal:2',
            'harga_pokok' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function scopeInStock($query)
    {
        return $query->where('stok', '>', 0);
    }

    public function isLowStock(int $threshold = 10): bool
    {
        return $this->stok <= $threshold;
    }
}
