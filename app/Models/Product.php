<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $casts = ['in_stock' => 'boolean', 'price' => 'decimal:2'];
    protected $fillable = ['name', 'price', 'category_id', 'in_stock', 'rating'];
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
