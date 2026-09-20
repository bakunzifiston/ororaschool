<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\PublicPages;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('public.about', [
            'page' => PublicPages::about(),
        ]);
    }

    public function contact(): View
    {
        return view('public.contact', [
            'page' => PublicPages::contact(),
        ]);
    }
}
