<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        $countries = Country::get(['name', 'iso2']);
        return view('auth.register', ['countries' => $countries]);
    }

    // Add other authentication methods as needed
}
