<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminProfileRequest;
use App\Support\DemoData\People;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();

        return view('admin.profile.edit', [
            'page' => [
                'header' => [
                    'breadcrumb' => [
                        ['label' => 'FarmSchool', 'route' => 'admin.dashboard'],
                        ['label' => 'Profile'],
                    ],
                    'title' => 'Profile',
                    'subtitle' => 'Your name, email and district on this estate.',
                ],
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'district' => $user->district ?? '',
                ],
                'districts' => People::districts(),
            ],
        ]);
    }

    public function update(UpdateAdminProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->only(['name', 'email', 'district']);

        if (filled($request->validated('password'))) {
            $data['password'] = $request->validated('password');
        }

        $user->update($data);

        return redirect()->route('admin.profile')
            ->with('status', 'Your profile was saved.');
    }
}
