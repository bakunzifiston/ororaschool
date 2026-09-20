<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\PublicHomePage;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.home', [
            'page' => PublicHomePage::data(),
        ]);
    }
}
