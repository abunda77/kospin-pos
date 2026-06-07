<?php

namespace App\Http\Controllers;

class InfoController extends Controller
{
    /**
     * Display phpinfo() output.
     */
    public function index()
    {
        return view('info');
    }
}
