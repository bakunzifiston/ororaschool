<?php

namespace App\Support\DemoData\Pages;

use App\Support\Rwanda;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * One method per unauthenticated page. All copy lives here rather than in the
 * Blade files, so the interface voice can be reviewed and rewritten in one place
 * and the views stay pure composition.
 */
class AuthPages
{
    public static function login(): array
    {
        return [
            'title' => 'Sign in',
            'email' => [
                'label' => 'Email address',
                'placeholder' => 'name@gemura.rw',
            ],
            'password' => [
                'label' => 'Password',
            ],
            'remember' => 'Keep me signed in on this device',
            'rememberHint' => 'Leave this off on a shared or borrowed phone.',
            'submit' => 'Sign in',
            'forgot' => 'Forgotten your password?',
            'footer' => 'No account yet?',
            'footerLink' => 'Create an account',
        ];
    }

    public static function forgotPassword(): array
    {
        return [
            'title' => 'Reset your password',
            'step' => 'Step 1 of 2',
            'subtitle' => 'Tell us the email address on your account and we will send a link to set a new '
                .'password. The link works for 60 minutes.',
            'email' => [
                'label' => 'Email address',
                'placeholder' => 'name@gemura.rw',
            ],
            // Grounded in the actual audience: plenty of field staff and
            // cooperative members have no working inbox of their own.
            'aside' => 'No email inbox of your own? Ask your academy coordinator or cooperative secretary '
                .'to reset it for you — they can do it from the workspace.',
            'submit' => 'Email me a reset link',
            'back' => 'Back to sign in',
        ];
    }

    public static function resetPassword(string $token, string $email = ''): array
    {
        $subtitle = $email !== ''
            ? 'Choose a new password for '.$email.'. You will be signed in once it is saved.'
            : 'Choose a new password. You will be signed in once it is saved.';

        return [
            'title' => 'Set a new password',
            'step' => 'Step 2 of 2',
            'token' => $token,
            'email' => $email,
            'subtitle' => $subtitle,
            'fields' => [
                'password' => [
                    'label' => 'New password',
                    'hint' => 'At least 10 characters. Avoid your phone number, your national ID or a birth year — those are the first things anyone guesses.',
                ],
                'confirmation' => [
                    'label' => 'New password again',
                    'hint' => 'Typed twice so a slip on a small keyboard does not lock you out.',
                ],
            ],
            'submit' => 'Save password and sign in',
            'back' => 'Back to sign in',
        ];
    }

    public static function verifyNotice(string $email): array
    {
        return [
            'title' => 'Confirm your email address',
            'email' => $email,
            'subtitle' => 'We sent a confirmation link to '.$email
                .'. Open it and you will land straight on your dashboard.',
            'reasons' => [
                'Certificates are issued to this address, so it has to be one you can open.',
                'Live session joining links are sent here an hour before a clinic starts.',
            ],
            'aside' => 'Nothing arrived yet? On some networks it takes a few minutes. Check your spam folder '
                .'before resending.',
            'resend' => 'Send the link again',
            'wrongAddress' => 'Wrong address? Ask your coordinator to correct it.',
        ];
    }

    public static function verified(string $email): array
    {
        return [
            'title' => 'Email confirmed',
            'email' => $email,
            'subtitle' => $email.' is confirmed. Certificates and live session links will '
                .'come to this address from now on.',
            'next' => [
                'Pick up the course closest to finishing on your dashboard.',
                'Your training record follows you to any Orora academy you join later.',
            ],
            'submit' => 'Go to my dashboard',
        ];
    }

    /**
     * Direct sign-up for a new learner.
     */
    public static function register(): array
    {
        return [
            'title' => 'Get your FarmSchool account',
            'fields' => [
                'first_name' => ['label' => 'First name', 'placeholder' => 'e.g. Placide'],
                'last_name' => ['label' => 'Last name', 'placeholder' => 'e.g. Bizimana'],
                'district' => ['label' => 'District'],
                'sector' => ['label' => 'Sector'],
                'email' => ['label' => 'Email address', 'placeholder' => 'name@example.rw', 'hint' => 'Your certificates are issued to this address.'],
                'password' => ['label' => 'Choose a password', 'hint' => 'At least 10 characters.'],
            ],
            'districts' => Rwanda::districts(),
            'sectorsByDistrict' => Rwanda::sectorsByDistrict(),
            'submit' => 'Create my account',
            'footer' => 'Already have an account?',
            'footerLink' => 'Sign in',
        ];
    }
}
