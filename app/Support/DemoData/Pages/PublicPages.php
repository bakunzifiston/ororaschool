<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\PublicCatalog;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 */
class PublicPages
{
    public static function about(): array
    {
        return [
            'header' => PublicHeader::make(
                'About Orora School',
                'The training and certification layer for the Orora ecosystem.',
            ),
            'platforms' => PublicCatalog::activePlatforms(),
        ];
    }

    public static function contact(): array
    {
        return [
            'header' => PublicHeader::make(
                'Contact / support',
                'For enrolment, a lost certificate number, or a course that will not open, write to the school desk.',
            ),
            'email' => 'help@ororaschool.rw',
            'phone' => '+250 788 000 110',
        ];
    }

    public static function certificateLookup(): array
    {
        return [
            'header' => PublicHeader::make(
                'Certificate verification',
                'Enter the number printed on the certificate. No login is required. A valid result shows the learner’s name, the course, the platform and the issue date — nothing else.',
            ),
        ];
    }
}
