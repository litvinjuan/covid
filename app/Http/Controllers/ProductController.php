<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Store\Models\Product;

class ProductController extends Controller
{
    public function list()
    {
        return view('product.list');
    }

    public function view(Product $product)
    {
        return view('product.view')
            ->with('product', $product);
    }

    public function create(CreateProductRequest $request)
    {
        $product = Product::query()->create([
            'supplier_id' => auth()->user()->id,
            'title' => $request->title(),
            'sku' => $request->sku(),
            'price' => $request->price(),
            'stock' => $request->stock(),
        ]);

        return redirect()->route('product.view', ['product' => $product]);
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        $product->update([
            'title' => $request->title(),
            'sku' => $request->sku(),
            'price' => $request->price(),
            'stock' => $request->stock(),
        ]);

        return redirect()->route('product.view', ['product' => $product]);
    }

    public function delete(Product $product)
    {
        $product->delete();

        return redirect()->route('product.list');
    }
}
