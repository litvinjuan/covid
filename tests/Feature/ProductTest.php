<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Store\Models\Product;
use Store\Models\User;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testProductCanBeCreated()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/products', $this->data());

        $product = Product::first();
        $this->assertNotNull($product);
        $response->assertRedirect("/products/{$product->id}");
        $this->assertCount(1, Product::all());
        $this->assertEquals($user->id, $product->owner->id);
        $this->assertEquals('My Product Title', $product->title);
        $this->assertEquals('a0001', $product->sku);
        $this->assertEquals(19999, $product->price);
        $this->assertEquals(5, $product->stock);
    }

    /** @test */
    public function testProductCanBeCreatedWithoutStock()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post('/products', array_merge($this->data(), ['stock' => null]));

        $product = Product::first();
        $response->assertRedirect("/products/{$product->id}");
        $this->assertEquals(0, $product->stock);
    }

    /** @test */
    public function testProductCanBeUpdated()
    {
        $user = factory(User::class)->create();
        $product = factory(Product::class)->create(['owner_id' => $user->id]);

        $response = $this->actingAs($user)->post("/products/{$product->id}/update", [
            'title' => 'My New Product Title',
            'sku' => 'a0002',
            'price' => 299.99,
            'stock' => 10,
        ]);

        $response->assertRedirect("/products/{$product->id}");
        $this->assertCount(1, Product::all());
        $this->assertEquals('My New Product Title', Product::first()->title);
        $this->assertEquals('a0002', Product::first()->sku);
        $this->assertEquals(29999, Product::first()->price);
        $this->assertEquals(10, Product::first()->stock);
    }

    /** @test */
    public function testProductsCanBeDeleted()
    {
        $user = factory(User::class)->create();
        $product = factory(Product::class)->create(['owner_id' => $user->id]);

        $response = $this->actingAs($user)->post("/products/{$product->id}/delete");

        $response->assertRedirect('/products');
        $this->assertCount(0, Product::all());
        $this->assertDatabaseCount('products', 1);
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
