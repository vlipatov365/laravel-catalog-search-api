<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function paginationInformation(Request $request, array $paginated, array $default): array
    {
        return [
            'meta' => [
                'current_page' => $paginated['current_page'],
                'per_page'     => $paginated['per_page'],
                'total'        => $paginated['total'],
                'last_page'    => $paginated['last_page'],
            ],
        ];
    }
}
