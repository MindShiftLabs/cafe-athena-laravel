<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Map 'email' input to 'user_email' column for Auth::attempt
        // 'password' key is required by Auth::attempt to hash check against the model's password
        if (Auth::attempt(['user_email' => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            $role = Auth::user()->user_role;

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($role === 'barista') {
                return redirect()->route('barista.dashboard');
            } elseif ($role === 'customer') {
                return redirect()->route('customer.dashboard');
            }

            return redirect()->intended(route('home')); 
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:user,user_email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'user_firstname' => $request->firstname,
            'user_lastname' => $request->lastname,
            'user_email' => $request->email,
            'user_password' => Hash::make($request->password),
            'user_role' => 'customer', // Default role
        ]);

        Auth::login($user);

        return redirect(route('home'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}