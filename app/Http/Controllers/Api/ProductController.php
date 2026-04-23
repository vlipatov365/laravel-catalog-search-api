<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Resources\ProductCollection;
use App\Services\ProductFilterService;

class ProductController extends Controller
{
    public function index(ProductIndexRequest $request, ProductFilterService $service): ProductCollection
    {
        $data = $request->validated();
        $query = $service->apply($data);
        $paginator = $query->with('category')
            ->paginate($data['per_page'] ?? 15);

        return new ProductCollection($paginator);
    }
}
