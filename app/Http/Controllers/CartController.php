<?php

namespace App\Http\Controllers;

use Store\Models\CartItem;
use Store\Models\Product;

class CartController extends Controller
{
    public function add()
    {
        $product = Product::query()->findOrFail(request('product'));
        $quantity = request('quantity', 1);

        $item = auth()->user()
            ->cart
            ->items()
            ->firstOrNew(['product_id' => $product->id], ['quantity' => 0]);

        $item->quantity += $quantity; // Set new quantity or add to previous one if available

        if ($product->outOfStock()) {
            return redirect()->back()->withErrors(['stock' => 'El producto está fuera de stock. Por favor, seleccioná otro.']);
        }

        if ($product->stock < $quantity) {
            return redirect()->back()->withErrors(['quantity' => 'No hay stock suficiente para este producto']);
        }

        $item->save();

        return redirect("/carrito");
    }

    public function update(Product $product)
    {
        $quantity = request('quantity');

        /** @var CartItem $item */
        $item = auth()->user()
            ->cart
            ->items()
            ->whereProductId($product->id)
            ->firstOrFail();

        if ($product->outOfStock()) {
            return redirect()->back()->withErrors(['stock' => 'El producto está fuera de stock. Por favor, seleccioná otro.']);
        }

        if ($product->stock < $quantity) {
            return redirect()->back()->withErrors(['quantity' => 'No hay stock suficiente para este producto']);
        }

        $item->update([
            'quantity' => $quantity,
        ]);

        return redirect("/carrito");
    }

    public function delete(Product $product)
    {
        auth()->user()
            ->cart
            ->items()
            ->whereProductId($product->id)
            ->delete();

        return redirect('/carrito');
    }
}
