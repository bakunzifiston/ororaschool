<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\PublicResourcesPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        return view('public.resources.index', [
            'page' => PublicResourcesPage::index(
                filters: [
                    'platform' => (string) $request->string('platform'),
                    'type' => (string) $request->string('type'),
                    'q' => (string) $request->string('q'),
                ],
                page: $request->integer('page', 1),
                empty: $request->boolean('empty'),
            ),
        ]);
    }
}
