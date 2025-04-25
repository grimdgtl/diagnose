<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Prikazuje početnu stranicu (home).
     */
    public function index()
    {
        return view('home');
    }
}
