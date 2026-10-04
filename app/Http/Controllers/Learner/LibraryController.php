<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Support\DemoData\LearnerProgress;
use App\Support\DemoData\LiveSessions;
use App\Support\DemoData\Pages\LearnerCertificatesPage;
use App\Support\DemoData\Pages\LearnerProfilePage;
use App\Support\DemoData\Pages\LearnerResourcesPage;
use App\Support\DemoData\Pages\LearnerSessionsPage;
use App\Support\DemoData\Resources;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LibraryController extends Controller
{
    public function certificates(Request $request): View
    {
        return view('learner.certificates.index', [
            'page' => LearnerCertificatesPage::index(empty: $request->boolean('empty')),
        ]);
    }

    public function sessions(Request $request): View
    {
        return view('learner.sessions.index', [
            'page' => LearnerSessionsPage::index(empty: $request->boolean('empty')),
        ]);
    }

    public function join(int $session): RedirectResponse
    {
        $row = LiveSessions::find($session);
        abort_unless($row, 404);

        $slugs = array_column(LearnerProgress::enrolments(), 'course');
        abort_unless(in_array($row['course_slug'], $slugs, true), 404);

        return redirect()->route('learner.sessions')
            ->with('status', 'Joining '.$row['title'].'. The meeting URL is a placeholder in this build.');
    }

    public function resources(Request $request): View
    {
        return view('learner.resources.index', [
            'page' => LearnerResourcesPage::index(
                type: (string) $request->string('type'),
                empty: $request->boolean('empty'),
            ),
        ]);
    }

    public function showResource(string $resource): View
    {
        $page = LearnerResourcesPage::show($resource);

        abort_unless($page !== null, 404);

        return view('learner.resources.show', ['page' => $page]);
    }

    public function fileResource(Request $request, string $resource): BinaryFileResponse|Response
    {
        abort_unless(LearnerProgress::canOpenResource($resource), 404);

        $row = Resources::find($resource);

        abort_unless($row !== null && Resources::canStream($row), 404);

        return Resources::stream($row, $request->boolean('download'));
    }

    public function profile(): View
    {
        return view('learner.profile.index', [
            'page' => LearnerProfilePage::data(),
        ]);
    }

    public function updateProfile(): RedirectResponse
    {
        return redirect()->route('learner.profile')
            ->with('status', 'Profile saved. Nothing was written in this build.');
    }
}
