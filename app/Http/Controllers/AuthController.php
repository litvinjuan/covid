<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Store\Models\Customer;
use Store\Models\User;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        if (! Auth::attempt($request->credentials())) {
            return redirect()->back()->withErrors(['Your credentials do not match.']);
        }

        /** @var User $user */
        $user = Auth::user();

        return redirect()->route('home');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        /** @var Customer $user */
        $user = Customer::query()->create([
            'email' => $request->email(),
            'password' => Hash::make($request->password()),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('auth.login');
    }
}
