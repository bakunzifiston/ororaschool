<?php

namespace App\Http\Controllers;

use App\Support\DemoData\Pages\SuperAdminDashboard;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        return view('admin.dashboard', [
            'page' => SuperAdminDashboard::data(empty: $request->boolean('empty')),
        ]);
    }
}
