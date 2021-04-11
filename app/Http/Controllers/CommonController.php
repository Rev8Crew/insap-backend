<?php

namespace App\Http\Controllers;


/**
 * Class CommonController
 *
 * @package App\Http\Controllers
 */
class CommonController extends Controller
{
    // For public application
    public function app()
    {
        return view('app');
    }
}
