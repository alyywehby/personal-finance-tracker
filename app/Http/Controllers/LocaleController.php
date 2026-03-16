<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller {
    public function update(Request $request) {
        $request->validate(['locale' => 'required|in:en,ar']);
        $locale = $request->locale;

        $request->session()->put('locale', $locale);

        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }

        App::setLocale($locale);

        return back();
    }
}
