<?php

namespace App\Livewire;

use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function switchLanguage($locale)
    {
        if (auth()->check()) {
            auth()->user()->update(['locale' => $locale]);
        }
        
        session()->put('locale', $locale);
        app()->setLocale($locale);
        
        return redirect()->back();
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
