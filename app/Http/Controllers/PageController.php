<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function areneBot(): View
    {
        return view('pages.arene-bot');
    }

    public function aPropos(): View
    {
        return view('pages.a-propos');
    }
}
