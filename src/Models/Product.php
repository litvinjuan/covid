<?php

namespace Store\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Store\Events\CartItemLowOnStock;
use Store\Events\CartItemProductNotEnoughStock;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function outOfStock(): bool
    {
        return $this->stock === 0;
    }

    public function updateStock($stock)
    {
        CartItem::query()
            ->where('quantity', '>', $stock)
            ->whereProductId($this->id)
            ->get()
            ->each(function ($item) {
                event(new CartItemProductNotEnoughStock($item));
            });

        CartItem::query()
            ->where('quantity', '<=', $stock)
            ->where('quantity', '>=', $stock / 1.2)
            ->whereProductId($this->id)
            ->get()
            ->each(function ($item) {
                event(new CartItemLowOnStock($item));
            });

        $this->update(['stock' => $stock]);
    }
}
