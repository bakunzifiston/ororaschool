<?php

namespace App\Support\DemoData;

use App\Support\DemoData\Pages\AuthPages;
use Illuminate\Support\Facades\Session;

/**
 * TEMPORARY — DELETE WITH THE REST OF THE FIXTURE LAYER.
 *
 * ---------------------------------------------------------------------------
 * Stands in for signing in. The unauthenticated pages have no credentials to
 * check, so the login form carries a preview selector and this class remembers
 * the chosen persona long enough to send the browser to the matching shell.
 *
 * When backend Phase 1 adds real authentication, delete:
 *   - this class
 *   - resources/views/components/dev/  (the visible preview control)
 *   - the preview_as handling in App\Http\Controllers\AuthPagesController
 * ---------------------------------------------------------------------------
 */
class Preview
{
    private const KEY = 'demo.preview_as';

    public static function remember(?string $experience): string
    {
        $experience = array_key_exists((string) $experience, AuthPages::previewOptions())
            ? $experience
            : 'learner';

        Session::put(self::KEY, $experience);

        return $experience;
    }

    /**
     * Whoever was last previewed. Defaults to the learner: that is who lands on
     * an email-verification screen in practice.
     */
    public static function experience(): string
    {
        return Session::get(self::KEY, 'learner');
    }

    public static function dashboardUrl(?string $experience = null): string
    {
        $experience = $experience ?? self::experience();

        return match ($experience) {
            'super-admin' => route('admin.dashboard'),
            'platform-workspace' => route('workspace.dashboard', [
                // The workspace persona's first platform, so the landing page is
                // one they actually work on.
                'platform' => DemoData::currentUser('platform-workspace')['platforms'][0],
            ]),
            default => route('learner.dashboard'),
        };
    }

    public static function label(?string $experience = null): string
    {
        $experience = $experience ?? self::experience();

        return AuthPages::previewOptions()[$experience]['label'] ?? 'Learner';
    }
}
