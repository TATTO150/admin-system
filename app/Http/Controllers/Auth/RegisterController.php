<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PragmaRX\Google2FA\Google2FA;

class RegisterController extends Controller
{
    use RegistersUsers {
        register as registration;
    }

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    // Mostrar la vista de registro
    public function showRegisterForm()
    {
        return view('google2fa.register'); // Asegúrate de que esta vista existe
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'google2fa_secret' => $data['google2fa_secret'], // Ensure you have google2fa_secret field in your users table migration
        ]);

        $user->enableTwoFactorAuthentication();

        return $user;
    }

  
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
  
        $google2fa = app('pragmarx.google2fa');
  
        $registration_data = $request->all();
  
        $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();
  
        $request->session()->flash('registration_data', $registration_data);
  
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $registration_data['email'],
           $registration_data['google2fa_secret']
        );

        $secret = $registration_data['google2fa_secret'];
        
        return view('google2fa.register')->with([
            'QR_Image' => $QR_Image,
            'secret' => $secret
        ]);
        
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function completeRegistration(Request $request)
    {        
        $request->merge(session('registration_data'));
  
        return $this->registration($request);
    }

}