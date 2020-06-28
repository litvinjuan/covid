<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Store\Models\Product;

class AddToCartRequest extends FormRequest
{
    const PRODUCT = 'product';
    const QUANTITY = 'quantity';

    /** @var Product */
    private $product;

    public function rules()
    {
        return [
            self::PRODUCT => 'required|exists:products,id',
            self::QUANTITY => 'required|integer|min:1',
        ];
    }

    public function product(): Product
    {
        if (! $this->product) {
            $this->product = Product::query()->findOrFail($this->input(self::PRODUCT));
        }

        return $this->product;
    }

    public function quantity(): int
    {
        return $this->input(self::QUANTITY);
    }
}
