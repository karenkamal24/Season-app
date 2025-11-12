<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Language switcher route - works in both web and admin panel
Route::middleware(['web'])->group(function () {
    Route::get('/switch-language/{locale}', function ($locale) {
        if (in_array($locale, ['ar', 'en'])) {
            session()->put('locale', $locale);
            
            if (Auth::check()) {
                Auth::user()->update(['locale' => $locale]);
            }
        }
        
        // Get the referer URL or default to admin panel
        $redirectUrl = request()->header('Referer') ?? url('/admin');
        
        return redirect($redirectUrl);
    })->name('switch-language');
});

// Terms and Privacy routes
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/privacy', 'pages.privacy')->name('privacy');
