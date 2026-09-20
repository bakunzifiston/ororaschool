<?php

namespace App\Http\Controllers;

use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\Pages\PublicHeader;
use App\Support\DemoData\Pages\PublicPages;
use App\Support\DemoData\Platforms;
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
        $row = IssuedCertificates::findByCode($code);
        $platform = $row ? Platforms::find($row['platform']) : null;

        if (! $row) {
            return view('public.verify', [
                'page' => [
                    'header' => PublicHeader::make(
                        'Not a valid certificate',
                        'This number is not on the Orora School register.',
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
                    'subtitle' => 'This number is not on the Orora School register.',
                    'message' => 'Check the code on the printed certificate. A mistyped digit is the usual reason a lookup fails.',
                ],
            ]);
        }

        $valid = $row['status'] === 'valid';

        $title = $valid ? 'Certificate verified' : 'Certificate revoked';
        $subtitle = $valid
            ? 'This number was minted on '.($platform['name'] ?? $row['platform']).'.'
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
                'code' => $row['code'],
                'learner' => $row['learner'],
                'course' => $row['course'],
                'issued_at' => $row['issued'],
                'status' => $row['status'],
                'platform' => $platform['name'] ?? $row['platform'],
                'title' => $title,
                'subtitle' => $subtitle,
            ],
        ]);
    }
}
