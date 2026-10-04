<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Support\DemoData\Pages\PublicHeader;
use App\Support\DemoData\Pages\PublicPages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $code = trim((string) $request->string('code'));

        if ($code !== '') {
            return redirect()->route('certificates.verify', ['code' => $code]);
        }

        return view('public.certificates.lookup', [
            'page' => PublicPages::certificateLookup(),
        ]);
    }

    public function show(string $code): View
    {
        $certificate = Certificate::query()
            ->with(['platform', 'course'])
            ->where('code', $code)
            ->first();

        if (! $certificate) {
            return view('public.verify', [
                'page' => [
                    'header' => PublicHeader::make(
                        'Not a valid certificate',
                        'This number is not on the FarmSchool register.',
                        [
                            ['label' => 'Certificate verification', 'route' => 'certificates.lookup'],
                            ['label' => 'Not a valid certificate'],
                        ],
                    ),
                    'found' => false,
                    'valid' => false,
                    'status' => 'not_found',
                    'code' => $code,
                    'title' => 'Not a valid certificate',
                    'subtitle' => 'This number is not on the FarmSchool register.',
                    'message' => 'Check the code on the printed certificate. A mistyped digit is the usual reason a lookup fails.',
                ],
            ]);
        }

        $valid = $certificate->status === 'valid';
        $platformName = $certificate->platform->name;

        $title = $valid ? 'Certificate verified' : 'Certificate revoked';
        $subtitle = $valid
            ? 'This number was minted on '.$platformName.'.'
            : 'This number was minted, then revoked. It is no longer valid.';

        return view('public.verify', [
            'page' => [
                'header' => PublicHeader::make(
                    $title,
                    $subtitle,
                    [
                        ['label' => 'Certificate verification', 'route' => 'certificates.lookup'],
                        ['label' => $title],
                    ],
                ),
                'found' => true,
                'valid' => $valid,
                'code' => $certificate->code,
                'learner' => $certificate->learner_name,
                'course' => $certificate->course->title,
                'issued_at' => $certificate->issued_at->format('j M Y'),
                'status' => $certificate->status,
                'platform' => $platformName,
                'title' => $title,
                'subtitle' => $subtitle,
            ],
        ]);
    }
}
