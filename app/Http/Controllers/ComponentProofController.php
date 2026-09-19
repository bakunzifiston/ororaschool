<?php

namespace App\Http\Controllers;

use App\Support\DemoData\Pages\ComponentProof;
use Illuminate\View\View;

class ComponentProofController extends Controller
{
    /**
     * The same page rendered inside whichever layout is named, which is how the
     * per-experience accent variation can be compared side by side.
     */
    public function index(string $experience = 'super-admin'): View
    {
        return view('design.components', [
            'experience' => $experience,
            'page' => ComponentProof::data(),
        ]);
    }
}
