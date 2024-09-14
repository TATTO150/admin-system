<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ConfrimacionContrasenaController extends Controller
{
    /**
     * Show the confirm password view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show()
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (Hash::check($request->password, $user->Contrasena)) {
            $request->session()->put('auth.password_confirmed_at', time());
            return redirect()->intended();
        }

        return back()->withErrors([
            'password' => __('La contraseña no coincide.'),
        ]);
    }
}
