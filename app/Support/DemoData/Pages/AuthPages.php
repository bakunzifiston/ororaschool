<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * One method per unauthenticated page. All copy lives here rather than in the
 * Blade files, so the interface voice can be reviewed and rewritten in one place
 * and the views stay pure composition.
 */
class AuthPages
{
    /**
     * The address a fixture visitor is recovering or verifying. Real flows read
     * this from the signed token or the authenticated user.
     */
    public static function pendingEmail(): string
    {
        return 'p.bizimana@umuhinzi.rw';
    }

    public static function login(): array
    {
        return [
            'title' => 'Sign in',
            'subtitle' => 'Orora School is the training and certification arm of the Orora platforms. '
                .'Use the account your platform coordinator set up for you.',
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
            'footerLink' => 'See how to get access',
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
            'aside' => 'No email inbox of your own? Ask your platform coordinator or cooperative secretary '
                .'to reset it for you — they can do it from the workspace.',
            'submit' => 'Email me a reset link',
            'back' => 'Back to sign in',
        ];
    }

    public static function resetPassword(string $token): array
    {
        return [
            'title' => 'Set a new password',
            'step' => 'Step 2 of 2',
            'token' => $token,
            'email' => self::pendingEmail(),
            'subtitle' => 'Choose a new password for '.self::pendingEmail().'. You will be signed in once it is saved.',
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

    public static function verifyNotice(): array
    {
        return [
            'title' => 'Confirm your email address',
            'email' => self::pendingEmail(),
            'subtitle' => 'We sent a confirmation link to '.self::pendingEmail()
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

    public static function verified(): array
    {
        return [
            'title' => 'Email confirmed',
            'email' => self::pendingEmail(),
            'subtitle' => self::pendingEmail().' is confirmed. Certificates and live session links will '
                .'come to this address from now on.',
            'next' => [
                'Pick up the course closest to finishing on your dashboard.',
                'Your training record follows you to any Orora platform you join later.',
            ],
            'submit' => 'Go to my dashboard',
        ];
    }

    /**
     * Registration leads with platform linking, because most learners already
     * have an account on the platform they use day to day. The direct sign-up
     * form below it is a separate decision — see the note in the Blade file.
     */
    public static function register(): array
    {
        return [
            'title' => 'Get your Orora School account',
            'subtitle' => 'Most learners already have one through the platform they use day to day. '
                .'Link that account and your training record and certificates follow you.',
            'platforms' => array_map(fn (array $platform) => [
                'slug' => $platform['slug'],
                'name' => $platform['name'],
                'discipline' => $platform['discipline'],
            ], Platforms::active()),
            'linkNote' => 'Linking takes you to that platform to confirm it is you. Nothing is shared back '
                .'except your name and the district you work in.',
            'divider' => 'Not on any of those yet?',
            'directIntro' => 'Create an account directly. You can link a platform later without losing '
                .'anything you have already finished.',
            'fields' => [
                'name' => ['label' => 'Full name', 'placeholder' => 'e.g. Placide Bizimana'],
                'district' => ['label' => 'District you work in'],
                'email' => ['label' => 'Email address', 'placeholder' => 'name@example.rw', 'hint' => 'Your certificates are issued to this address.'],
                'password' => ['label' => 'Choose a password', 'hint' => 'At least 10 characters.'],
            ],
            'districts' => People::districts(),
            'submit' => 'Create my account',
            'footer' => 'Already have an account?',
            'footerLink' => 'Sign in',
        ];
    }

    /**
     * TEMPORARY — the preview selector's options. Removed with real auth.
     */
    public static function previewOptions(): array
    {
        return [
            'super-admin' => [
                'label' => 'Super Admin',
                'detail' => 'all four platforms',
            ],
            'platform-workspace' => [
                'label' => 'Platform Admin',
                'detail' => 'the Gemura workspace',
            ],
            'learner' => [
                'label' => 'Learner',
                'detail' => 'a dairy farmer mid-course',
            ],
        ];
    }
}
