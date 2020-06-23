<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Store\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        $credentials = [
            'email' => request('email'),
            'password' => request('password'),
        ];

        if (! Auth::attempt($credentials)) {
            return redirect()->back()->with('Wrong email or password.');
        }

        return redirect('/home');
    }

    public function register()
    {
        $user = User::query()->create([
            'email' => request('email'),
            'password' => Hash::make(request('password')),
        ]);

        Auth::login($user, true);

        return redirect('/home');
    }

    public function logout()
    {
        Auth::logout();

        return redirect('/login');
    }
}
