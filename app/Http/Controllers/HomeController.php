<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function home()
    {
        return view('home')
            ->with('user', current_customer());
    }
}
