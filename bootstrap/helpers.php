<?php

use Illuminate\Support\Facades\Auth;
use Store\Models\Admin;
use Store\Models\Customer;
use Store\Models\Supplier;
use Store\Models\User;

if (! function_exists('current_user')) {
    function current_user(): ?User
    {
        /** @var User|null $user */
        static $user;

        if (! $user) {
            $user = Auth::user();
        }

        return $user;
    }
}

if (! function_exists('current_admin')) {
    function current_admin(): ?Admin
    {
        /** @var Admin|null $admin */
        static $admin;

        if (! $admin) {
            $admin = Auth::user();
        }

        return $admin;
    }
}

if (! function_exists('current_supplier')) {
    function current_supplier(): ?Supplier
    {
        /** @var Supplier|null $supplier */
        static $supplier;

        if (! $supplier) {
            $supplier = Auth::user();
        }

        return $supplier;
    }
}

if (! function_exists('current_customer')) {
    function current_customer(): ?Customer
    {
        /** @var Customer|null $customer */
        static $customer;

        if (! $customer) {
            $customer = Auth::user();
        }

        return $customer;
    }
}
