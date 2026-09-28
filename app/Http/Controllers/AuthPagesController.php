<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Support\DemoData\Pages\AuthPages;
use App\Support\DemoData\Platforms;
use App\UserRole;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthPagesController extends Controller
{
    public function login(): View
    {
        return view('auth.login', ['page' => AuthPages::login()]);
    }

    public function attemptLogin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended($request->user()->dashboardUrl());
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

    public function storeRegistration(RegisterRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            ...$request->safe()->only(['name', 'district', 'email', 'password']),
            'role' => UserRole::Learner,
            'status' => 'active',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('status', 'Account created. Confirm your email address to finish.');
    }

    public function forgotPassword(): View
    {
        return view('auth.forgot-password', ['page' => AuthPages::forgotPassword()]);
    }

    public function sendResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'If that address has an Orora School account, a reset link is on its '
            .'way. It works for 60 minutes.');
    }

    public function resetPassword(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'page' => AuthPages::resetPassword($token, $request->string('email')->toString()),
        ]);
    }

    public function updatePassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
                Auth::login($user);
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($request->user()->dashboardUrl())
            ->with('status', 'Password saved. You are signed in with the new one.');
    }

    public function verifyNotice(Request $request): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($request->user()->dashboardUrl());
        }

        return view('auth.verify-email', [
            'page' => AuthPages::verifyNotice($request->user()->email),
        ]);
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($request->user()->dashboardUrl());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Confirmation link sent again to '.$request->user()->email.'.');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('verification.verified');
    }

    public function verified(Request $request): View|RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return view('auth.verified', [
            'page' => AuthPages::verified($request->user()->email),
            'dashboardUrl' => $request->user()->dashboardUrl(),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Signed out.');
    }
}
