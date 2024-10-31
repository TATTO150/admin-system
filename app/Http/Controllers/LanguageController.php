<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    // Método para mostrar la vista de selección de idioma
    public function showLanguageOptions()
    {
        return view('idioma');
    }

    // Método para cambiar el idioma
    public function changeLanguage(Request $request)
    {
        $lang = $request->input('lang');
        
        // Verifica que el idioma seleccionado esté permitido
        if (in_array($lang, ['en', 'es'])) { // Agrega otros idiomas según sea necesario
            Session::put('locale', $lang);
            App::setLocale($lang);
        }

        return redirect()->route('idioma')->with('success', 'Idioma cambiado correctamente');
    }
}
