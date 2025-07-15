<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function dologin(Request $request)
    {
        // ✅ Valider les données d'entrée
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ✅ Tenter l'authentification avec "remember me" facultatif
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // protège contre les attaques de session fixation
            return redirect()->intended(route('request.index'))->with('success', 'Connexion réussie.');
        }

        // ❌ Erreur : identifiants invalides
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput();
    }

    public function register()
    {
        $countries = Country::get(['name', 'iso2']);
        return view('auth.register', ['countries' => $countries]);
    }

    public function doRegister(Request $request)
    {
        $validated = $request->validate([
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'passport_country' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female,Other',
            'marital_status' => 'required|in:Single,Married,Divorced,Widowed',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            'terms_accepted' => 'accepted',
            'birth_date' => ['required', 'date', 'before:today'],
        ]);

        $user = User::create([
            'last_name' => $validated['last_name'],
            'first_name' => $validated['first_name'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'passport_country' => $validated['passport_country'],
            'nationality' => $validated['nationality'],
            'phone_number' => $validated['phone_number'],
            'gender' => $validated['gender'],
            'marital_status' => $validated['marital_status'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'terms_accepted' => true,
            'birth_date' => $validated['birth_date'],
        ]);

        Auth::login($user);

        return redirect()->intended(route('home'))->with('success', 'Inscription réussie, bienvenue !');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('success', 'Déconnexion réussie.');
    }
}
