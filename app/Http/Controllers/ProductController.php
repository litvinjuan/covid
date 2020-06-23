<?php

namespace App\Http\Controllers;

use Store\Models\Product;

class ProductController extends Controller
{
    public function create()
    {
        $product = Product::query()->create([
            'owner_id' => auth()->user()->id,
            'title' => request('title'),
            'sku' => request('sku'),
            'price' => request('price'),
        ]);

        return redirect("/products/{$product->id}");
    }

    public function update(Product $product)
    {
        $product->update([
            'title' => request('title'),
            'sku' => request('sku'),
            'price' => request('price'),
        ]);

        return redirect("/products/{$product->id}");
    }

    public function view(Product $product)
    {
        return view('welcome');
    }

    public function delete(Product $product)
    {
        $product->delete();

        return redirect('/products');
    }
}
