<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Estate-wide activity. Entries match actions from earlier phases: a course
 * published, a role assigned, a platform created, a certificate issued.
 */
class ActivityLogs
{
    public static function all(): array
    {
        return [
            ['id' => 1, 'actor' => 'Jean-Baptiste Habimana', 'user' => 'Jean-Baptiste Habimana', 'action' => 'course.submitted', 'action_label' => 'submitted a course for review', 'target' => 'Heat Detection and AI Timing for Ankole–Friesian Crosses', 'platform' => 'Gemura', 'platform_slug' => 'gemura', 'at' => '18 minutes ago', 'date' => '19 Sep 2026, 15:41'],
            ['id' => 2, 'actor' => 'Olivier Mugisha', 'user' => 'Olivier Mugisha', 'action' => 'course.approved', 'action_label' => 'approved a course', 'target' => 'Animal Identification and Ear-Tag Registration', 'platform' => 'BuchaPro', 'platform_slug' => 'buchapro', 'at' => '2 hours ago', 'date' => '19 Sep 2026, 13:58'],
            ['id' => 3, 'actor' => 'Josiane Kayitesi', 'user' => 'Josiane Kayitesi', 'action' => 'enrollment.created', 'action_label' => 'enrolled 34 learners into', 'target' => 'Cold Chain Discipline at Milk Collection Centres', 'platform' => 'Gemura', 'platform_slug' => 'gemura', 'at' => '4 hours ago', 'date' => '19 Sep 2026, 11:52'],
            ['id' => 4, 'actor' => 'Diane Iradukunda', 'user' => 'Diane Iradukunda', 'action' => 'course.published', 'action_label' => 'published a course', 'target' => 'Least-Cost Ration Formulation with Local Ingredients', 'platform' => 'FeedGrid', 'platform_slug' => 'feedgrid', 'at' => 'Yesterday', 'date' => '18 Sep 2026, 16:04'],
            ['id' => 5, 'actor' => 'Aline Mukamana', 'user' => 'Aline Mukamana', 'action' => 'course.archived', 'action_label' => 'archived a course', 'target' => 'Mineral Supplementation for Lactating Cows', 'platform' => 'FeedGrid', 'platform_slug' => 'feedgrid', 'at' => '2 days ago', 'date' => '17 Sep 2026, 09:20'],
            ['id' => 6, 'actor' => 'Thierry Munyaneza', 'user' => 'Thierry Munyaneza', 'action' => 'course.created', 'action_label' => 'created a course', 'target' => 'Cooperative Membership and Share Records', 'platform' => 'OroraFarm', 'platform_slug' => 'ororafarm', 'at' => '3 days ago', 'date' => '16 Sep 2026, 14:11'],
            ['id' => 7, 'actor' => 'Gloriose Mukandayisenga', 'user' => 'Gloriose Mukandayisenga', 'action' => 'role.assigned', 'action_label' => 'assigned Content Manager on Gemura to', 'target' => 'Claudine Uwimana', 'platform' => 'Gemura', 'platform_slug' => 'gemura', 'at' => '3 days ago', 'date' => '16 Sep 2026, 10:02'],
            ['id' => 8, 'actor' => 'Gloriose Mukandayisenga', 'user' => 'Gloriose Mukandayisenga', 'action' => 'role.assigned', 'action_label' => 'assigned Instructor on OroraFarm to', 'target' => 'Jean-Baptiste Habimana', 'platform' => 'OroraFarm', 'platform_slug' => 'ororafarm', 'at' => '4 days ago', 'date' => '15 Sep 2026, 17:44'],
            ['id' => 9, 'actor' => 'Alice Mukeshimana', 'user' => 'Alice Mukeshimana', 'action' => 'certificate.issued', 'action_label' => 'issued certificate OS-GEM-2026-1847 to', 'target' => 'Immaculée Nyiransabimana', 'platform' => 'Gemura', 'platform_slug' => 'gemura', 'at' => '5 days ago', 'date' => '14 Sep 2026, 12:30'],
            ['id' => 10, 'actor' => 'Gloriose Mukandayisenga', 'user' => 'Gloriose Mukandayisenga', 'action' => 'platform.created', 'action_label' => 'created a platform', 'target' => 'Ishyiga', 'platform' => 'Ishyiga', 'platform_slug' => 'ishyiga', 'at' => '6 months ago', 'date' => '14 Mar 2026, 09:00'],
            ['id' => 11, 'actor' => 'Gloriose Mukandayisenga', 'user' => 'Gloriose Mukandayisenga', 'action' => 'platform.deactivated', 'action_label' => 'deactivated a platform', 'target' => 'Ubworozi', 'platform' => 'Ubworozi', 'platform_slug' => 'ubworozi', 'at' => '3 months ago', 'date' => '12 Jun 2026, 16:18'],
            ['id' => 12, 'actor' => 'Solange Nyirahabimana', 'user' => 'Solange Nyirahabimana', 'action' => 'live_session.scheduled', 'action_label' => 'scheduled a live clinic', 'target' => 'Reading CMT paddles together', 'platform' => 'Gemura', 'platform_slug' => 'gemura', 'at' => '6 days ago', 'date' => '13 Sep 2026, 08:40'],
            ['id' => 13, 'actor' => 'Aline Mukamana', 'user' => 'Aline Mukamana', 'action' => 'user.invited', 'action_label' => 'invited', 'target' => 'Fabrice Gasana', 'platform' => 'BuchaPro', 'platform_slug' => 'buchapro', 'at' => '18 days ago', 'date' => '01 Sep 2026, 11:05'],
            ['id' => 14, 'actor' => 'Emmanuel Nshimiyimana', 'user' => 'Emmanuel Nshimiyimana', 'action' => 'settings.updated', 'action_label' => 'updated certificate numbering on', 'target' => 'FeedGrid', 'platform' => 'FeedGrid', 'platform_slug' => 'feedgrid', 'at' => '9 days ago', 'date' => '10 Sep 2026, 15:22'],
            ['id' => 15, 'actor' => 'John Bosco Nsengimana', 'user' => 'John Bosco Nsengimana', 'action' => 'course.published', 'action_label' => 'published a course', 'target' => 'Farm Record Keeping for Smallholder Enterprises', 'platform' => 'OroraFarm', 'platform_slug' => 'ororafarm', 'at' => '17 days ago', 'date' => '02 Sep 2026, 10:14'],
            ['id' => 16, 'actor' => 'Claudine Uwimana', 'user' => 'Claudine Uwimana', 'action' => 'course.published', 'action_label' => 'published a course', 'target' => 'Movement Permits and Livestock Transport Records', 'platform' => 'BuchaPro', 'platform_slug' => 'buchapro', 'at' => '10 days ago', 'date' => '09 Sep 2026, 13:07'],
            ['id' => 17, 'actor' => 'Josiane Kayitesi', 'user' => 'Josiane Kayitesi', 'action' => 'enrollment.created', 'action_label' => 'enrolled 18 learners into', 'target' => 'Mastitis Detection and Milk Hygiene in Smallholder Herds', 'platform' => 'Gemura', 'platform_slug' => 'gemura', 'at' => '11 days ago', 'date' => '08 Sep 2026, 09:55'],
            ['id' => 18, 'actor' => 'Alice Mukeshimana', 'user' => 'Alice Mukeshimana', 'action' => 'certificate.issued', 'action_label' => 'issued certificate OS-FED-2026-0521 to', 'target' => 'Béatrice Uwamahoro', 'platform' => 'FeedGrid', 'platform_slug' => 'feedgrid', 'at' => '12 days ago', 'date' => '07 Sep 2026, 16:48'],
            ['id' => 19, 'actor' => 'Gloriose Mukandayisenga', 'user' => 'Gloriose Mukandayisenga', 'action' => 'role.created', 'action_label' => 'created a custom role', 'target' => 'Certificate Officer', 'platform' => 'Orora School', 'platform_slug' => '', 'at' => '2 months ago', 'date' => '11 Jul 2026, 11:00'],
            ['id' => 20, 'actor' => 'Marie-Claire Uwase', 'user' => 'Marie-Claire Uwase', 'action' => 'platform.created', 'action_label' => 'created a platform', 'target' => 'Ubworozi', 'platform' => 'Ubworozi', 'platform_slug' => 'ubworozi', 'at' => '20 months ago', 'date' => '08 Jan 2025, 10:30'],
            ['id' => 21, 'actor' => 'Espérance Twagirayezu', 'user' => 'Espérance Twagirayezu', 'action' => 'course.published', 'action_label' => 'published a course', 'target' => 'Aflatoxin Control in Maize Bran Storage', 'platform' => 'FeedGrid', 'platform_slug' => 'feedgrid', 'at' => '7 weeks ago', 'date' => '28 Jul 2026, 14:19'],
            ['id' => 22, 'actor' => 'Chantal Ingabire', 'user' => 'Chantal Ingabire', 'action' => 'live_session.scheduled', 'action_label' => 'scheduled a live clinic', 'target' => 'Evening intake lactometer checks', 'platform' => 'Gemura', 'platform_slug' => 'gemura', 'at' => '8 days ago', 'date' => '11 Sep 2026, 07:40'],
            ['id' => 23, 'actor' => 'Vincent Dusabimana', 'user' => 'Vincent Dusabimana', 'action' => 'user.archived', 'action_label' => 'archived the account of', 'target' => 'Vincent Dusabimana', 'platform' => 'OroraFarm', 'platform_slug' => 'ororafarm', 'at' => '4 months ago', 'date' => '22 May 2026, 18:02'],
            ['id' => 24, 'actor' => 'Patrick Habineza', 'user' => 'Patrick Habineza', 'action' => 'user.invited', 'action_label' => 'requested activation for', 'target' => 'Ishyiga', 'platform' => 'Ishyiga', 'platform_slug' => 'ishyiga', 'at' => '6 days ago', 'date' => '13 Sep 2026, 11:28'],
        ];
    }
}
