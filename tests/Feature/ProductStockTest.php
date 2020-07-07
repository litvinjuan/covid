<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Store\Models\Product;
use Store\Models\User;
use Tests\TestCase;

class ProductStockTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testProductCanBeCreatedWithStock()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('product.create'), $this->data());

        $product = Product::first();
        $this->assertNotNull($product);
        $response->assertRedirect(route('product.view', ['product' => $product]));
        $this->assertEquals(5, $product->stock);
    }

    /** @test */
    public function testProductCanBeCreatedWithoutStock()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('product.create'), array_merge($this->data(), ['stock' => null]));

        $product = Product::first();
        $response->assertRedirect(route('product.view', ['product' => $product]));
        $this->assertEquals(0, $product->stock);
    }

    /** @test */
    public function testProductStockCanBeUpdated()
    {
        $product = factory(Product::class)->create();

        $response = $this->actingAs($product->supplier)->put(route('product.update', ['product' => $product]), [
            'title' => 'My New Product Title',
            'sku' => 'a0002',
            'price' => 299.99,
            'stock' => 10,
        ]);

        $response->assertRedirect(route('product.view', ['product' => $product]));
        $this->assertEquals(10, Product::first()->stock);
    }

    private function data()
    {
        return [
            'title' => 'My Product Title',
            'sku' => 'a0001',
            'price' => 199.99,
            'stock' => 5
        ];
    }
}
