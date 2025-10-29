<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebPageController extends Controller
{
    /**
     * Display Terms and Conditions page
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Display Privacy Policy page
     */
    public function privacy()
    {
        return view('pages.privacy');
    }
}

