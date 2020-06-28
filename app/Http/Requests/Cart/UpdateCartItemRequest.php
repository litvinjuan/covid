<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    const QUANTITY = 'quantity';

    public function rules()
    {
        return [
            self::QUANTITY => 'required|integer|min:1',
        ];
    }

    public function quantity(): int
    {
        return $this->input(self::QUANTITY);
    }
}
