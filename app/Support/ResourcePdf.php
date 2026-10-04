<?php

namespace App\Support;

/**
 * A one-page PDF from the fields already on a resource. Used when no file was uploaded.
 */
class ResourcePdf
{
    /**
     * @param  array<string, mixed>  $resource
     */
    public static function render(array $resource): string
    {
        $type = self::pdfText((string) ($resource['type_label'] ?? 'PDF'));
        $attached = self::pdfText((string) ($resource['attached_to'] ?? ''));
        $meta = self::pdfText(implode(' · ', array_values(array_filter([
            (string) ($resource['platform_name'] ?? ''),
            (string) ($resource['size'] ?? ''),
            (string) ($resource['updated'] ?? ''),
        ], fn (string $value): bool => $value !== ''))));

        $commands = [
            'BT',
            '/F1 10 Tf',
            '72 760 Td',
            '('.$type.') Tj',
            '/F1 18 Tf',
            '0 -28 Td',
        ];

        foreach (self::wrap((string) ($resource['title'] ?? ''), 42) as $index => $line) {
            if ($index > 0) {
                $commands[] = '0 -22 Td';
            }

            $commands[] = '('.self::pdfText($line).') Tj';
        }

        $commands[] = '/F1 11 Tf';
        $commands[] = '0 -24 Td';
        $commands[] = '('.$attached.') Tj';
        $commands[] = '0 -18 Td';
        $commands[] = '('.$meta.') Tj';
        $commands[] = 'ET';

        $stream = implode("\n", $commands);

        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            4 => '<< /Length '.strlen($stream).' >> stream'."\n".$stream."\n".'endstream',
            5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $number => $body) {
            $offsets[$number] = strlen($pdf);
            $pdf .= $number.' 0 obj '.$body."\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 6\n";
        $pdf .= "0000000000 65535 f \n";

        for ($number = 1; $number <= 5; $number++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$number]);
        }

        $pdf .= "trailer << /Size 6 /Root 1 0 R >>\n";
        $pdf .= "startxref\n".$xref."\n%%EOF\n";

        return $pdf;
    }

    public static function pdfText(string $text): string
    {
        $ascii = str_replace(
            ['·', '—', '–', '’', '‘'],
            ['-', '-', '-', "'", "'"],
            $text,
        );
        $ascii = preg_replace('/[^\x20-\x7E]/', '?', $ascii) ?? $ascii;

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $ascii);
    }

    /**
     * @return list<string>
     */
    public static function wrap(string $text, int $width): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }

            $next = $current === '' ? $word : $current.' '.$word;

            if (strlen($next) > $width && $current !== '') {
                $lines[] = $current;
                $current = $word;

                continue;
            }

            $current = $next;
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines === [] ? [''] : $lines;
    }
}
