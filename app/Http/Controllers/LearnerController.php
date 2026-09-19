<?php

namespace App\Http\Controllers;

use App\Support\DemoData\Pages\LearnerDashboard;
use Illuminate\View\View;

class LearnerController extends Controller
{
    public function dashboard(): View
    {
        return view('learner.dashboard', [
            'page' => LearnerDashboard::data(),
        ]);
    }
}
