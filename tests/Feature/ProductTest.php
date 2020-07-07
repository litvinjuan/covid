<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Store\Models\Product;
use Store\Models\Supplier;
use Store\Models\User;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testProductCanBeCreated()
    {
        $user = factory(Supplier::class)->create();

        $response = $this->actingAs($user)->post(route('product.create'), $this->data());

        $product = Product::first();
        $this->assertNotNull($product);
        $response->assertRedirect(route('product.view', ['product' => $product]));
        $this->assertCount(1, Product::all());
        $this->assertEquals($user->id, $product->supplier->id);
        $this->assertEquals('My Product Title', $product->title);
        $this->assertEquals('a0001', $product->sku);
        $this->assertEquals(19999, $product->price);
    }

    /** @test */
    public function testProductCanBeUpdated()
    {
        $product = factory(Product::class)->create();

        $response = $this->actingAs($product->supplier)->put(route('product.update', ['product' => $product]), [
            'title' => 'My New Product Title',
            'sku' => 'a0002',
            'price' => 299.99,
            'stock' => 10,
        ]);

        $response->assertRedirect(route('product.view', ['product' => $product]));
        $this->assertCount(1, Product::all());
        $this->assertEquals('My New Product Title', Product::first()->title);
        $this->assertEquals('a0002', Product::first()->sku);
        $this->assertEquals(29999, Product::first()->price);
    }

    /** @test */
    public function testProductsCanBeDeleted()
    {
        $product = factory(Product::class)->create();

        $response = $this->actingAs($product->supplier)->delete(route('product.view', ['product' => $product]));

        $response->assertRedirect(route('product.list'));
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
