<?php

namespace App\Http\Controllers;

class FrontendController extends Controller
{
    // For public application
    public function app()
    {
        return view('app');
    }
}
