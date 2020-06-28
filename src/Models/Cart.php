<?php

namespace Store\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Store\Exceptions\CartException;
use Store\Models\Traits\HasCartItems;

class Cart extends Model
{
    use SoftDeletes;
    use HasCartItems;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
