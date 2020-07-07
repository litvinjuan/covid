<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    const TITLE = 'title';
    const SKU = 'sku';
    const PRICE = 'price';
    const STOCK = 'stock';

    public function rules()
    {
        return [
            self::TITLE => 'required|string',
            self::SKU => 'required|string',
            self::PRICE => 'required|numeric|min:1',
            self::STOCK => 'required|numeric|min:0',
        ];
    }

    public function title(): string
    {
        return $this->input(self::TITLE);
    }

    public function sku(): string
    {
        return $this->input(self::SKU);
    }

    public function price(): int
    {
        return (int) ($this->input(self::PRICE) * 100);
    }

    public function stock(): int
    {
        return (int) $this->input(self::STOCK);
    }
}
