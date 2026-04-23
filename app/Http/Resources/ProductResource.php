<?php

namespace App\Http\Resources;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var Product $this
         */
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'price'       => $this->price,
            'in_stock'    => $this->in_stock,
            'rating'      => $this->rating,
            'category'    => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ],
        ];
    }
}
