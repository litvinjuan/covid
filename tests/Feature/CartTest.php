<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Store\Events\CartItemLowOnStock;
use Store\Events\CartItemProductNotEnoughStock;
use Store\Models\CartItem;
use Store\Models\Customer;
use Store\Models\Product;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testProductCanBeAddedToCart()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create();

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect(route('cart.view'));
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

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertRedirect(route('cart.view'));
        $this->assertCount(1, $user->cart->items);
        $this->assertEquals($product->id, $user->cart->items->first()->product->id);
        $this->assertEquals(1, $user->cart->items->first()->quantity);

        $product2 = factory(Product::class)->create(['stock' => 20]);

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product2->id,
            'quantity' => 5,
        ]);

        $user->load(['cart.items']); // Reload items so the new one is fetched

        $response->assertRedirect(route('cart.view'));
        $this->assertCount(2, $user->cart->items);
        $this->assertEquals($product2->id, $user->cart->items->get(1)->product->id);
        $this->assertEquals(5, $user->cart->items->get(1)->quantity);
    }

    /** @test */
    public function testProductsCanBeAddedTwiceToCart()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create();

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertRedirect(route('cart.view'));
        $this->assertCount(1, $user->cart->items);
        $this->assertEquals($product->id, $user->cart->items->first()->product->id);
        $this->assertEquals(3, $user->cart->items->first()->quantity);

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 4,
        ]);

        $user->load(['cart.items']); // Reload items so the new one is fetched

        $response->assertRedirect(route('cart.view'));
        $this->assertCount(1, $user->cart->items);
        $this->assertEquals($product->id, $user->cart->items->first()->product->id);
        $this->assertEquals(7, $user->cart->items->first()->quantity);
    }

    /** @test */
    public function testProductCanBeRemovedFromCart()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create();
        $product2 = factory(Product::class)->create();
        $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 1,
        ])->assertRedirect(route('cart.view'));
        $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product2->id,
            'quantity' => 1,
        ])->assertRedirect(route('cart.view'));
        $this->assertCount(2, $user->cart->items);

        $this->actingAs($user)->delete(route('cart.delete', ['product' => $product]))->assertRedirect(route('cart.view'));

        $user->load('cart.items'); // Reload items so the new one is fetched

        $this->assertCount(1, $user->cart->items);
        $this->assertEquals($product2->id, $user->cart->items->first()->product->id);
    }

    /** @test */
    public function testCanUpdateProductQuantityInCart()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 20]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 1,
        ])->assertRedirect(route('cart.view'));
        $this->assertCount(1, $user->cart->items);

        $this->actingAs($user)->put(route('cart.update', ['product' => $product]), [
            'quantity' => 5,
        ])->assertRedirect(route('cart.view'));

        $user->load('cart.items'); // Reload items so the new one is fetched

        $this->assertCount(1, $user->cart->items);
        $this->assertEquals(5, $user->cart->items->first()->quantity);
    }

    /** @test */
    public function testProductCannotBeAddedToCartIfOutOfStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 0]);

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors();
        $this->assertCount(0, $user->cart->items);
        $this->assertDatabaseCount('cart_items', 0);
    }

    /** @test */
    public function testProductCannotBeAddedToCartIfNotEnoughStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 5]);

        $response = $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 8, // More than current stock
        ]);

        $response->assertSessionHasErrors();
        $this->assertCount(0, $user->cart->items);
        $this->assertDatabaseCount('cart_items', 0);
    }

    /** @test */
    public function testProductStockCannotBeUpdatedIfNotEnoughStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 3]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 1, // Enough Stock
        ])->assertRedirect(route('cart.view'));
        $this->assertCount(1, $user->cart->items);

        $this->actingAs($user)->put(route('cart.update', ['product' => $product]), [
            'quantity' => 5, // Not Enough Stock
        ])->assertSessionHasErrors();

        $user->load('cart.items');

        $this->assertCount(1, $user->cart->items);
        $this->assertEquals(1, $user->cart->items->first()->quantity);
    }

    /** @test */
    public function testEventIsRaisedWhenStockIsLowerThanACartItem()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 20]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 20, // Enough Stock
        ])->assertRedirect(route('cart.view'));
        $this->assertCount(1, $user->cart->items);

        $this->expectsEvents(CartItemProductNotEnoughStock::class);
        $product->update(['stock' => 10]); // Cart's quantity is above the product's stock
    }

    /** @test */
    public function testUserIsNotNotifiedIfCartInItemHasEnoughStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 20]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 10,
        ])->assertRedirect(route('cart.view'));
        $this->assertCount(1, $user->cart->items);

        $this->doesntExpectEvents(CartItemLowOnStock::class);

        // Should only be raised when there is a 20% margin or less
        $product->update(['stock' => 13]);
    }

    /** @test */
    public function testUserIsNotifiedIfCartInItemHasLowStock()
    {
        $user = factory(Customer::class)->create();
        $product = factory(Product::class)->create(['stock' => 20]);
        $this->actingAs($user)->post(route('cart.add'), [
            'product' => $product->id,
            'quantity' => 10,
        ])->assertRedirect();
        $this->assertCount(1, $user->cart->items);

        $this->expectsEvents(CartItemLowOnStock::class);

        // Should only be raised when there is a 20% margin or less
        $product->update(['stock' => 12]);
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
