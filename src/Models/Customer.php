<?php

namespace Store\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Parental\HasParent;

class Customer extends User
{
    use HasParent;

    protected static function boot()
    {
        parent::boot();

        static::created(function (User $user) {
            $user->cart()->create();
        });
    }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'customer_id');
    }
}
