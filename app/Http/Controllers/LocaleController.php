<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LocaleController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'locale' => 'required|in:en,es',
        ]);

        $locale = $request->input('locale');

        // Persist in DB if authenticated
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        // Always set cookie
        return back()->withCookie('app_locale', $locale, 60 * 24 * 365);
    }
}
