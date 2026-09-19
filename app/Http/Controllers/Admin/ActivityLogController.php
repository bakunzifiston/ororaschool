<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\ActivityLogsPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.activity.index', [
            'page' => ActivityLogsPage::index(
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
                user: (string) $request->string('user'),
                platform: (string) $request->string('platform'),
                action: (string) $request->string('action'),
            ),
        ]);
    }
}
