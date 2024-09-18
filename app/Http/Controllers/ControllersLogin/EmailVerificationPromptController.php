<?php

namespace App\Http\Controllers\ControllersLogin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use App\Notifications\VerifyEmail;

class EmailVerificationPromptController extends Controller
{
    public function notice()
    {
        return view('auth.verify-email');
    }

    public function verify(Request $request)
    {
        $user = $request->user();

        if ($this->hasVerifiedEmail($user)) {
            return redirect()->route('dashboard');
        }

        if ($this->markEmailAsVerified($user)) {
            event(new Verified($user));
        }

        return redirect()->route('dashboard')->with('verified', true);
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($this->hasVerifiedEmail($user)) {
            return redirect()->route('dashboard');
        }

        $this->sendEmailVerificationNotification($user);

        return back()->with('status', 'verification-link-sent');
    }

    protected function markEmailAsVerified($user)
    {
        $user->Verificacion_Correo_Electronico = true;
        return $user->save();
    }

    protected function hasVerifiedEmail($user)
    {
        return $user->Verificacion_Correo_Electronico === true;
    }

    protected function sendEmailVerificationNotification($user)
    {
        $user->notify(new VerifyEmail);
    }
}
