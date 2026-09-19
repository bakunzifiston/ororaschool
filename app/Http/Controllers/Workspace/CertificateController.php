<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\Pages\CertificatesPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request, string $platform): View
    {
        return view('workspace.certificates.index', [
            'platformSlug' => $platform,
            'page' => CertificatesPage::index(
                $platform,
                empty: $request->boolean('empty'),
                page: max(1, $request->integer('page', 1)),
                status: (string) $request->string('status'),
            ),
        ]);
    }

    public function revoke(string $platform, string $certificate): RedirectResponse
    {
        $row = IssuedCertificates::findForPlatform($certificate, $platform);
        abort_unless($row, 404);

        return redirect()->route('workspace.certificates', ['platform' => $platform])
            ->with('status', $row['code'].' would be revoked. Nothing was written in this build.');
    }
}
