<?php

namespace App\Http\Controllers;

use App\Support\DemoData\Pages\AuthPages;
use App\Support\DemoData\Platforms;
use App\Support\DemoData\Preview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Unauthenticated pages for Phase F2.
 *
 * No credentials are checked and nothing is persisted. The POST methods exist so
 * every form has a real destination and the flash region shows what the response
 * would say; each one is replaced by the genuine action in backend Phase 1.
 */
class AuthPagesController extends Controller
{
    public function login(): View
    {
        return view('auth.login', ['page' => AuthPages::login()]);
    }

    /**
     * TEMPORARY: reads the preview selector and opens that shell. The whole
     * method goes when real authentication arrives.
     */
    public function attemptLogin(Request $request): RedirectResponse
    {
        $experience = Preview::remember($request->input('preview_as'));

        return redirect(Preview::dashboardUrl($experience))
            ->with('status', 'Opened the '.Preview::label($experience)
                .' shell. No credentials were checked — this build has no authentication yet.');
    }

    public function register(): View
    {
        return view('auth.register', ['page' => AuthPages::register()]);
    }

    /**
     * Platform linking — the primary route to an account.
     */
    public function linkPlatform(Request $request): RedirectResponse
    {
        $platform = Platforms::find((string) $request->input('platform'));

        return back()->with('status', $platform
            ? 'Linking with '.$platform['name'].' would hand you over to that platform to confirm '
                .'it is you. The integration itself arrives with the backend phases.'
            : 'Choose a platform to link.');
    }

    /**
     * Secondary route — only reachable if self-registration is supported.
     */
    public function storeRegistration(): RedirectResponse
    {
        return redirect()->route('verification.notice')
            ->with('status', 'Account created. Confirm your email address to finish — nothing was saved in this build.');
    }

    public function forgotPassword(): View
    {
        return view('auth.forgot-password', ['page' => AuthPages::forgotPassword()]);
    }

    public function sendResetLink(): RedirectResponse
    {
        // Worded so it reveals nothing about whether the address exists — the
        // copy real auth should keep.
        return back()->with('status', 'If that address has an Orora School account, a reset link is on its '
            .'way. It works for 60 minutes.');
    }

    public function resetPassword(string $token): View
    {
        return view('auth.reset-password', ['page' => AuthPages::resetPassword($token)]);
    }

    public function updatePassword(): RedirectResponse
    {
        return redirect()->route('login')
            ->with('status', 'Password saved. Sign in with the new one — nothing was changed in this build.');
    }

    public function verifyNotice(): View
    {
        return view('auth.verify-email', ['page' => AuthPages::verifyNotice()]);
    }

    public function resendVerification(): RedirectResponse
    {
        return back()->with('status', 'Confirmation link sent again to '.AuthPages::pendingEmail().'.');
    }

    public function verified(): View
    {
        return view('auth.verified', [
            'page' => AuthPages::verified(),
            'dashboardUrl' => Preview::dashboardUrl(),
            'previewLabel' => Preview::label(),
        ]);
    }
}
