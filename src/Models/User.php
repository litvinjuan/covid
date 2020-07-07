<?php

namespace Store\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Parental\HasChildren;

class User extends Authenticatable
{
    use Notifiable;
    use HasChildren;

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $childTypes = [
        'admin' => Admin::class,
        'customer' => Customer::class,
        'supplier' => Supplier::class,
    ];
}
