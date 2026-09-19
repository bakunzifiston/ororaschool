<?php

namespace App\Http\Controllers;

use App\Support\DemoData\Pages\SuperAdminDashboard;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'page' => SuperAdminDashboard::data(),
        ]);
    }
}
