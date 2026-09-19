<?php

namespace App\Http\Controllers;

use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\Platforms;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    public function show(string $code): View
    {
        $row = IssuedCertificates::findByCode($code);
        $platform = $row ? Platforms::find($row['platform']) : null;

        if (! $row) {
            return view('public.verify', [
                'page' => [
                    'found' => false,
                    'valid' => false,
                    'code' => $code,
                    'title' => 'Not a valid certificate',
                    'subtitle' => 'This number is not on the Orora School register.',
                    'message' => 'Check the code on the printed certificate. A mistyped digit is the usual reason a lookup fails.',
                ],
            ]);
        }

        $valid = $row['status'] === 'valid';

        return view('public.verify', [
            'page' => [
                'found' => true,
                'valid' => $valid,
                'code' => $row['code'],
                'learner' => $row['learner'],
                'course' => $row['course'],
                'issued_at' => $row['issued'],
                'status' => $row['status'],
                'platform' => $platform['name'] ?? $row['platform'],
                'title' => $valid ? 'Certificate verified' : 'Certificate revoked',
                'subtitle' => $valid
                    ? 'This number was minted on '.($platform['name'] ?? $row['platform']).'.'
                    : 'This number was minted, then revoked. It is no longer valid.',
            ],
        ]);
    }
}
