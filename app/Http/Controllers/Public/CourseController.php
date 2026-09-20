<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\PublicCatalogPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        return view('public.courses.index', [
            'page' => PublicCatalogPage::index(
                filters: [
                    'platform' => (string) $request->string('platform'),
                    'academy' => (string) $request->string('academy'),
                    'difficulty' => (string) $request->string('difficulty'),
                    'language' => (string) $request->string('language'),
                    'price' => (string) $request->string('price'),
                    'q' => (string) $request->string('q'),
                ],
                page: $request->integer('page', 1),
                empty: $request->boolean('empty'),
            ),
        ]);
    }

    public function show(string $course): View
    {
        $page = PublicCatalogPage::show($course);
        abort_unless($page, 404);

        return view('public.courses.show', [
            'page' => $page,
        ]);
    }
}
