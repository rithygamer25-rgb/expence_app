<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle an incoming authentication login attempt request.
     */
    public function login(Request $request)
    {
        // 1. Validate form fields
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Safely attempt session authentication verification
        $remember = $request->has('rememberMe');
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('home');
        }

        // 3. Fallback error mapping if login matching failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle account registration signup form processing.
     */
    public function register(Request $request)
    {
        // 1. Enforce strict database credential matching constraints
        $validatedData = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Instantiate new encrypted profile credentials instance structure
        $user = User::create([
            'name'     => $validatedData['name'],
            'email'    => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // 3. Automatically log in the user immediately upon registration
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Account registration complete!');
    }

    /**
     * Handle user log out operations and teardown open session handles safely.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
