<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class BlockedController extends Controller
{
    public function show()
    {
        return view('auth.bloqueo');
    }
}
