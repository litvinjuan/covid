<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use Store\Exceptions\CartException;
use Store\Models\Product;

class CartController extends Controller
{
    public function view()
    {
        return view('cart')
            ->with('user', current_customer())
            ->with('cart', current_customer()->cart);
    }

    public function add(AddToCartRequest $request)
    {
        try {
            current_customer()->cart->addItem($request->product(), $request->quantity());
        } catch (CartException $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }

        return redirect()->route('cart.view');
    }

    public function update(Product $product, UpdateCartItemRequest $request)
    {
        try {
            current_customer()->cart->updateItem($product, $request->quantity());
        } catch (CartException $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }

        return redirect()->route('cart.view');
    }

    public function delete(Product $product)
    {
        try {
            current_customer()->cart->deleteItem($product);
        } catch (CartException $exception) {
            return redirect()->back()->withErrors([$exception->getMessage()]);
        }

        return redirect()->route('cart.view');
    }
}
