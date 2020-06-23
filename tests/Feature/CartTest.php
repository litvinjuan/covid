<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Store\Events\CartItemLowOnStock;
use Store\Events\CartItemProductNotEnoughStock;
use Store\Models\CartItem;
use Store\Models\Customer;
use Store\Models\Product;
use Store\Models\User;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testProductCanBeAddedToCart()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create();

        $response = $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect();
        $this->assertCount(1, CartItem::all());
        $this->assertCount(1, $user->cart->items);
        $this->assertEquals($product->id, $user->cart->items->first()->product->id);
        $this->assertEquals(1, $user->cart->items->first()->quantity);
    }

    /** @test */
    public function testMultipleProductsCanBeAddedToCart()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create();

        $response = $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect();
        $this->assertCount(1, $user->cart->items);
        $this->assertEquals($product->id, $user->cart->items->first()->product->id);
        $this->assertEquals(1, $user->cart->items->first()->quantity);

        $product2 = factory(Product::class)->create(['stock' => 20]);

        $response = $this->actingAs($user)->post("/cart", [
            'product' => $product2->id,
            'quantity' => 5,
        ]);

        $user->load(['cart.items']); // Reload items so the new one is fetched

        $response->assertRedirect();
        $this->assertCount(2, $user->cart->items);
        $this->assertEquals($product2->id, $user->cart->items->get(1)->product->id);
        $this->assertEquals(5, $user->cart->items->get(1)->quantity);
    }

    /** @test */
    public function testProductCanBeRemovedFromCart()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create();
        $product2 = factory(Product::class)->create();
        $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();
        $this->actingAs($user)->post("/cart", [
            'product' => $product2->id,
            'quantity' => 1,
        ])->assertRedirect();
        $this->assertCount(2, $user->cart->items);

        $this->actingAs($user)->delete("/cart/{$product->id}")->assertRedirect();

        $user->load('cart.items'); // Reload items so the new one is fetched

        $this->assertCount(1, $user->cart->items);
        $this->assertEquals($product2->id, $user->cart->items->first()->product->id);
    }

    /** @test */
    public function testCanUpdateProductQuantityInCart()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 20]);
        $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 1,
        ])->assertRedirect();
        $this->assertCount(1, $user->cart->items);

        $this->actingAs($user)->put("/cart/{$product->id}", [
            'quantity' => 5,
        ])->assertRedirect();

        $user->load('cart.items'); // Reload items so the new one is fetched

        $this->assertCount(1, $user->cart->items);
        $this->assertEquals(5, $user->cart->items->first()->quantity);
    }

    /** @test */
    public function testProductCannotBeAddedToCartIfOutOfStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 0]);

        $response = $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('stock');
        $this->assertCount(0, $user->cart->items);
        $this->assertDatabaseCount('cart_items', 0);
    }

    /** @test */
    public function testProductCannotBeAddedToCartIfNotEnoughStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 5]);

        $response = $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 8, // More than current stock
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertCount(0, $user->cart->items);
        $this->assertDatabaseCount('cart_items', 0);
    }

    /** @test */
    public function testProductStockCannotBeUpdatedIfNotEnoughStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 3]);
        $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 1, // Enough Stock
        ])->assertRedirect();
        $this->assertCount(1, $user->cart->items);

        $this->actingAs($user)->put("/cart/{$product->id}", [
            'quantity' => 5, // Not Enough Stock
        ])->assertSessionHasErrors('quantity');

        $user->load('cart.items');

        $this->assertCount(1, $user->cart->items);
        $this->assertEquals(1, $user->cart->items->first()->quantity);
    }

    /** @test */
    public function testEventIsRaisedWhenStockIsLowerThanACartItem()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 20]);
        $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 20, // Enough Stock
        ])->assertRedirect();
        $this->assertCount(1, $user->cart->items);

        $this->expectsEvents(CartItemProductNotEnoughStock::class);
        $product->updateStock(10); // Cart's quantity is above the product's stock
    }

    /** @test */
    public function testUserIsNotNotifiedIfCartInItemHasEnoughStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 20]);
        $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 10,
        ])->assertRedirect();
        $this->assertCount(1, $user->cart->items);

        $this->doesntExpectEvents(CartItemLowOnStock::class);

        // Should only be raised when there is a 20% margin or less
        $product->updateStock(13);
    }

    /** @test */
    public function testUserIsNotifiedIfCartInItemHasLowStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 20]);
        $this->actingAs($user)->post("/cart", [
            'product' => $product->id,
            'quantity' => 10,
        ])->assertRedirect();
        $this->assertCount(1, $user->cart->items);

        $this->expectsEvents(CartItemLowOnStock::class);

        // Should only be raised when there is a 20% margin or less
        $product->updateStock(12);
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
