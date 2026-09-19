<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\GlobalContentPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.content.index', [
            'page' => GlobalContentPage::index(
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
            ),
        ]);
    }
}
