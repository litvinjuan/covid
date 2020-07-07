<?php

namespace Store\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Store\Exceptions\CartException;

class CartItem extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        if ($this->product->outOfStock()) {
            throw CartException::productOutOfstock();
        }

        if ($this->product->stock < $this->quantity) {
            throw CartException::productNotEnoughStock();
        }

        $this->save();

        return $this;
    }
}
