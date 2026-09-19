<?php

namespace App\Support\DemoData\Pages;

use App\Support\DemoData\Courses;
use App\Support\DemoData\People;
use App\Support\DemoData\Platforms;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Feeds the component proof page. Every component gets two unrelated fixture
 * sets — different columns, different lengths, different states — so the page
 * demonstrates the components are data-driven rather than shaped around one
 * example.
 */
class ComponentProof
{
    public static function data(): array
    {
        return [
            'header' => [
                'breadcrumb' => [
                    ['label' => 'Orora School', 'route' => 'admin.dashboard'],
                    ['label' => 'Settings', 'route' => 'admin.settings'],
                    ['label' => 'Component proof'],
                ],
                'title' => 'Component proof',
                'subtitle' => 'Every shared component rendered against two unrelated fixture sets. Switch the accent by visiting this page from the workspace or learner experience.',
            ],

            // ---- x-stat-card -------------------------------------------------
            'stats' => [
                'a' => [
                    'caption' => 'Set A — estate totals, mixed trend directions',
                    'items' => [
                        ['label' => 'Learners enrolled', 'value' => '10,412', 'trend' => '+486', 'direction' => 'up', 'note' => 'last 30 days'],
                        ['label' => 'Certificates issued', 'value' => '1,847', 'trend' => '−62', 'direction' => 'down', 'note' => 'vs August'],
                        ['label' => 'Awaiting review', 'value' => '2', 'trend' => 'Oldest 4 days', 'direction' => null, 'note' => null],
                    ],
                ],
                'b' => [
                    'caption' => 'Set B — single platform, no trend, long labels',
                    'items' => [
                        ['label' => 'Milk collection centres covered', 'value' => '37', 'trend' => null, 'direction' => null, 'note' => 'Musanze and Burera'],
                        ['label' => 'Average completion', 'value' => '74%', 'trend' => '+4 pts', 'direction' => 'up', 'note' => null],
                    ],
                ],
            ],

            // ---- x-status-badge ---------------------------------------------
            'statuses' => [
                'a' => ['caption' => 'Set A — content lifecycle', 'items' => ['draft', 'pending_review', 'approved', 'published', 'archived']],
                'b' => ['caption' => 'Set B — enrolment states and an unmapped value', 'items' => ['active', 'completed', 'suspended']],
            ],

            // ---- x-progress-bar ---------------------------------------------
            'progress' => [
                'a' => [
                    'caption' => 'Set A — labelled, with meta line',
                    'items' => [
                        ['value' => 9, 'label' => 'Aflatoxin Control in Maize Bran Storage', 'meta' => '1 of 13 lessons · started yesterday'],
                        ['value' => 72, 'label' => 'Mastitis Detection and Milk Hygiene', 'meta' => '13 of 18 lessons · due 28 Sep 2026'],
                        ['value' => 100, 'label' => 'Animal Identification and Ear-Tag Registration', 'meta' => 'Finished 14 Aug 2026 · certificate issued'],
                    ],
                ],
                'b' => [
                    'caption' => 'Set B — unlabelled, compact, cohort averages',
                    'items' => [
                        ['value' => 45, 'label' => null, 'meta' => 'Kinigi collection centre'],
                        ['value' => 88, 'label' => null, 'meta' => 'Butaro field officers'],
                    ],
                ],
            ],

            // ---- x-role-chip -------------------------------------------------
            'roles' => [
                'a' => ['caption' => 'Set A — elevated and standard, with scope', 'items' => ['super-admin', 'platform-owner', 'curriculum-lead'], 'scope' => true, 'removable' => false],
                'b' => ['caption' => 'Set B — assignment editor, removable, no scope', 'items' => ['instructor', 'reviewer', 'field-coordinator', 'learner'], 'scope' => false, 'removable' => true],
            ],

            // ---- x-course-card ----------------------------------------------
            'courseCards' => [
                'a' => [
                    'caption' => 'Set A — staff context: instructor and length in the footer',
                    'items' => [
                        ['course' => Courses::find('cold-chain-collection-centres'), 'platform' => 'Gemura · Dairy and livestock', 'progress' => null],
                        ['course' => Courses::find('traceable-chain-kraal-abattoir'), 'platform' => 'BuchaPro · Livestock traceability', 'progress' => null],
                    ],
                ],
                'b' => [
                    'caption' => 'Set B — learner context: the same component shows progress instead',
                    'items' => [
                        ['course' => Courses::find('least-cost-ration-formulation'), 'platform' => 'FeedGrid', 'progress' => 38, 'status' => 'active'],
                        ['course' => Courses::find('animal-identification-eartags'), 'platform' => 'BuchaPro', 'progress' => 100, 'status' => 'completed'],
                    ],
                ],
            ],

            // ---- x-data-table -----------------------------------------------
            'tables' => [
                'a' => [
                    'caption' => 'Set A — six columns, person and status cells, single page',
                    'columns' => [
                        ['key' => 'name', 'label' => 'Platform'],
                        ['key' => 'discipline', 'label' => 'Discipline'],
                        ['key' => 'steward', 'label' => 'Platform owner', 'type' => 'person'],
                        ['key' => 'learners', 'label' => 'Learners', 'align' => 'right', 'numeric' => true],
                        ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                    ],
                    'rows' => Platforms::all(),
                    'pagination' => ['page' => 1, 'pages' => 1, 'from' => 1, 'to' => count(Platforms::all()), 'total' => count(Platforms::all())],
                ],
                'b' => [
                    'caption' => 'Set B — different columns, progress and role cells, page 2 of 4',
                    'columns' => [
                        ['key' => 'name', 'label' => 'Learner', 'type' => 'person'],
                        ['key' => 'district', 'label' => 'District'],
                        ['key' => 'progress', 'label' => 'Progress', 'type' => 'progress'],
                        ['key' => 'status', 'label' => 'Enrolment', 'type' => 'status'],
                    ],
                    'rows' => array_slice(People::learners(), 2, 4),
                    'pagination' => ['page' => 2, 'pages' => 4, 'from' => 8, 'to' => 11, 'total' => 14],
                ],
                'c' => [
                    'caption' => 'Set C — no rows, so the table falls back to its empty state',
                    'columns' => [
                        ['key' => 'name', 'label' => 'Learner', 'type' => 'person'],
                        ['key' => 'status', 'label' => 'Enrolment', 'type' => 'status'],
                    ],
                    'rows' => [],
                    'pagination' => null,
                ],
            ],

            // ---- x-empty-state ----------------------------------------------
            'emptyStates' => [
                'a' => [
                    'icon' => 'video',
                    'title' => 'No live sessions scheduled',
                    'message' => 'Field officers book onto clinics up to two weeks ahead. Schedule one and it will appear here with its registration link.',
                    'action' => 'Schedule a live session',
                ],
                'b' => [
                    'icon' => 'award',
                    'title' => 'Your first certificate is close',
                    'message' => 'Finish the last five lessons of Mastitis Detection and Milk Hygiene and your certificate will be issued here the same day.',
                    'action' => 'Continue the course',
                ],
            ],

            // ---- modal shell -------------------------------------------------
            'modals' => [
                'a' => [
                    'name' => 'archive-course',
                    'trigger' => 'Archive a course',
                    'title' => 'Archive this course?',
                    'subtitle' => 'Mineral Supplementation for Lactating Cows',
                    'body' => '74 learners are part-way through. Archiving hides the course from the catalogue but lets those cohorts finish and still receive certificates.',
                    'confirm' => 'Archive course',
                    'variant' => 'danger',
                ],
                'b' => [
                    'name' => 'invite-instructor',
                    'trigger' => 'Invite an instructor',
                    'title' => 'Invite an instructor to Gemura',
                    'subtitle' => 'They will be able to draft and submit courses for review',
                    'body' => 'The same shell, a different job: a wider panel, a form in the body, and two footer actions. Nothing about the dialog is specific to either case.',
                    'confirm' => 'Send invitation',
                    'variant' => 'primary',
                ],
            ],
        ];
    }
}
