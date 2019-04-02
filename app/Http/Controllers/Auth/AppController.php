<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AppController extends Controller
{
    public function index(): View
    {
        return view('app', []);
    }
}
