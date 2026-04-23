<?php

use App\Models\Category;
use App\Models\Product;

it('returns 200 and correct structure', function () {
    $category = Category::factory()->create();
    Product::factory()->count(3)->create(['category_id' => $category->id]);

    $this->getJson('/api/products')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'price', 'in_stock', 'rating', 'category'],
            ],
            'meta' => ['current_page', 'per_page', 'total', 'last_page'],
        ]);
});
it('filters by search query', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['name' => 'Wireless Keyboard', 'category_id' => $category->id]);
    Product::factory()->create(['name' => 'USB Mouse', 'category_id' => $category->id]);

    $this->getJson('/api/products?q=Keyboard')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Wireless Keyboard');
});

it('filters by in_stock', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['in_stock' => true, 'category_id' => $category->id]);
    Product::factory()->create(['in_stock' => false, 'category_id' => $category->id]);

    $this->getJson('/api/products?in_stock=true')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.in_stock', true);
});
it('filters by price range', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['price' => 100, 'category_id' => $category->id]);
    Product::factory()->create(['price' => 500, 'category_id' => $category->id]);
    Product::factory()->create(['price' => 1000, 'category_id' => $category->id]);

    $this->getJson('/api/products?price_from=200&price_to=600')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.price', '500.00');
});
it('filters by rating_from', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['rating' => 3.0, 'category_id' => $category->id]);
    Product::factory()->create(['rating' => 4.5, 'category_id' => $category->id]);
    Product::factory()->create(['rating' => 2.0, 'category_id' => $category->id]);

    $this->getJson('/api/products?rating_from=4')
        ->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.rating', 4.5);
});

it('sorts by price ascending', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['price' => 300, 'category_id' => $category->id]);
    Product::factory()->create(['price' => 100, 'category_id' => $category->id]);
    Product::factory()->create(['price' => 200, 'category_id' => $category->id]);

    $response = $this->getJson('/api/products?sort=price_asc')
        ->assertStatus(200);

    $prices = collect($response->json('data'))->pluck('price');
    expect($prices->first())->toBeLessThan($prices->last());
});
it('returns correct meta total after filtering', function () {
    $category = Category::factory()->create();
    Product::factory()->count(3)->create(['in_stock' => true, 'category_id' => $category->id]);
    Product::factory()->count(7)->create(['in_stock' => false, 'category_id' => $category->id]);

    $this->getJson('/api/products?in_stock=true')
        ->assertStatus(200)
        ->assertJsonPath('meta.total', 3);
});
it('returns 422 for invalid sort value', function () {
    $this->getJson('/api/products?sort=invalid_value')
        ->assertStatus(422)
        ->assertJsonValidationErrors(['sort']);
});
it('returns 422 when per_page exceeds maximum', function () {
    $this->getJson('/api/products?per_page=200')
        ->assertStatus(422)
        ->assertJsonValidationErrors(['per_page']);
});
