<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\TwoFactorAuthenticatable;


class TwoFactorController extends Controller
{
    public function show(){
        return view('auth.two-factor-challenge');
    }

    public function store(Request $request)
{
  
}
}
