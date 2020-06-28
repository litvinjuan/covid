<?php

namespace Store\Models\Traits;

use Store\Exceptions\CartException;
use Store\Models\CartItem;
use Store\Models\Product;

trait HasCartItems
{
    public function addItem(Product $product, int $quantity): CartItem
    {
        /** @var CartItem $item */
        $item = $this->items()
            ->firstOrNew(['product_id' => $product->id], ['quantity' => 0]);

        return $this->setQuantity($item, $item->quantity + $quantity);
    }

    public function updateItem(Product $product, int $quantity): CartItem
    {
        /** @var CartItem $item */
        $item = $this->items()
            ->where('product_id', '=', $product->id)
            ->firstOrFail();

        return $this->setQuantity($item, $quantity);
    }

    public function deleteItem(Product $product): void
    {
        /** @var CartItem $item */
        $item = $this->items()
            ->where('product_id', '=', $product->id)
            ->delete();
    }

    private function setQuantity(CartItem $item, int $quantity): CartItem
    {
        $item->quantity = $quantity;

        if ($item->product->outOfStock()) {
            throw CartException::productOutOfstock();
        }

        if ($item->product->stock < $item->quantity) {
            throw CartException::productNotEnoughStock();
        }

        $item->save();

        return $item;
    }
}
