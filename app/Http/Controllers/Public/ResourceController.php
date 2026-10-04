<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoData\Pages\PublicResourcesPage;
use App\Support\DemoData\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        return view('public.resources.index', [
            'page' => PublicResourcesPage::index(
                filters: [
                    'platform' => (string) $request->string('platform'),
                    'type' => (string) $request->string('type'),
                    'q' => (string) $request->string('q'),
                ],
                page: $request->integer('page', 1),
                empty: $request->boolean('empty'),
            ),
        ]);
    }

    public function show(string $resource): View
    {
        $page = PublicResourcesPage::show($resource);

        abort_unless($page !== null, 404);

        return view('public.resources.show', ['page' => $page]);
    }

    public function file(Request $request, string $resource): BinaryFileResponse|Response
    {
        $row = Resources::find($resource);

        abort_unless($row !== null && Resources::isPubliclyOpen($row) && Resources::canStream($row), 404);

        return Resources::stream($row, $request->boolean('download'));
    }
}
