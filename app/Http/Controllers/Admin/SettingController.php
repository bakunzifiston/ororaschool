<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\SettingsPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'page' => SettingsPage::data(),
        ]);
    }

    public function update(): RedirectResponse
    {
        return redirect()->route('admin.settings')
            ->with('status', 'Settings saved. Nothing was written in this build.');
    }
}
