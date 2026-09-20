<?php

namespace App\Http\Controllers;

use App\Support\DemoData\Pages\LearnerDashboard;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LearnerController extends Controller
{
    public function dashboard(Request $request): View
    {
        return view('learner.dashboard', [
            'page' => LearnerDashboard::data(empty: $request->boolean('empty')),
        ]);
    }
}
