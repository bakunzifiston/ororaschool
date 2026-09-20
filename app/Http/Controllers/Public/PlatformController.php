<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\PublicPlatformPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function index(): View
    {
        return view('public.platforms.index', [
            'page' => PublicPlatformPage::index(),
        ]);
    }

    public function show(Request $request, string $platform): View
    {
        $page = PublicPlatformPage::show(
            $platform,
            filters: [
                'academy' => (string) $request->string('academy'),
                'difficulty' => (string) $request->string('difficulty'),
                'language' => (string) $request->string('language'),
                'price' => (string) $request->string('price'),
                'q' => (string) $request->string('q'),
            ],
            page: $request->integer('page', 1),
            empty: $request->boolean('empty'),
        );
        abort_unless($page, 404);

        return view('public.platforms.show', [
            'page' => $page,
        ]);
    }
}
