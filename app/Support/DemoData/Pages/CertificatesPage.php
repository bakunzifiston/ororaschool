<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\IssuedCertificates;
use App\Support\DemoData\Paging;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class CertificatesPage
{
    public static function index(string $platform, bool $empty = false, int $page = 1, string $status = ''): array
    {
        $current = Platforms::find($platform) ?? Platforms::find('gemura');
        $rows = $empty ? [] : IssuedCertificates::forPlatform($platform);

        if ($status !== '') {
            $rows = array_values(array_filter($rows, fn (array $row) => $row['status'] === $status));
        }

        $paged = Paging::paginate(
            $rows,
            $page,
            10,
            '/workspace/'.$platform.'/certificates',
            array_filter(['empty' => $empty ? 1 : null, 'status' => $status !== '' ? $status : null]),
        );

        return [
            'platform' => $current,
            'header' => WorkspaceHeader::make(
                $current,
                'Certificates',
                'Numbers minted on '.$current['name'].'. A revoked certificate still resolves on the public verification page.',
            ),
            'filters' => [
                'status' => $status,
                'statuses' => ['' => 'Any status', 'valid' => 'Valid', 'revoked' => 'Revoked'],
            ],
            'rows' => $paged['rows'],
            'pagination' => $paged['pagination'],
            'emptyTitle' => 'No certificates issued on '.$current['name'].' yet',
            'emptyMessage' => 'When a learner finishes an eligible course, a number is minted against the global format and appears here.',
        ];
    }
}
