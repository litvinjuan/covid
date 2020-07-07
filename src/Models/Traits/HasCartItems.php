<?php

namespace Store\Models\Traits;

use Store\Models\CartItem;
use Store\Models\Product;

trait HasCartItems
{
    public function addItem(Product $product, int $quantity): CartItem
    {
        /** @var CartItem $item */
        $item = $this->items()
            ->firstOrNew(['product_id' => $product->id], ['quantity' => 0]);

        $quantity = $item->quantity + $quantity;
        $item->setQuantity($quantity);

        return $item;
    }

    public function updateItem(Product $product, int $quantity): CartItem
    {
        /** @var CartItem $item */
        $item = $this->items()
            ->where('product_id', '=', $product->id)
            ->firstOrFail();

        return $item->setQuantity($quantity);
    }

    public function deleteItem(Product $product): void
    {
        /** @var CartItem $item */
        $this->items()
            ->where('product_id', '=', $product->id)
            ->delete();
    }
}
