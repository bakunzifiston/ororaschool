<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\PermissionsPage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.permissions.index', [
            'page' => PermissionsPage::index(empty: $request->boolean('empty')),
        ]);
    }
}
