<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductFilterService
{
    public function apply(array $filters): Builder
    {
        $query = Product::query()
            ->when($filters['q'] ?? null, fn ($q, $value) => $q->where('name', 'like', "%{$value}%"))
            ->when($filters['price_from'] ?? null, fn ($q, $value) => $q->where('price', '>=', $value))
            ->when($filters['price_to'] ?? null, fn ($q, $value) => $q->where('price', '<=', $value))
            ->when($filters['category_id'] ?? null, fn ($q, $value) => $q->where('category_id', $value))
            ->when(isset($filters['in_stock']), fn ($q) => $q->where('in_stock', $filters['in_stock']))
            ->when($filters['rating_from'] ?? null, fn ($q, $value) => $q->where('rating', '>=', $value));
        $sortMap = [
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
            'rating_desc' => ['rating', 'desc'],
            'newest' => ['created_at', 'desc'],
        ];
        [$column, $direction] = $sortMap[$filters['sort'] ?? 'newest'] ?? $sortMap['newest'];
        $query->orderBy($column, $direction);

        return $query;
    }
}
