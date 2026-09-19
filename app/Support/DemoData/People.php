<?php

namespace App\Support\DemoData;

/**
 * FIXTURE LAYER — DELETE WHEN REAL DATA ARRIVES.
 *
 * Staff, instructors and learners. `roles` is keyed by platform slug, mirroring
 * the shape user_platform_roles will eventually have. A person can hold a
 * different role on each platform — that is the John Doe / Alice pattern from
 * the architecture (here: Jean-Baptiste and Claudine).
 */
class People
{
    public static function staff(): array
    {
        return array_values(array_filter(
            self::directory(),
            fn (array $person) => ($person['kind'] ?? 'staff') !== 'learner',
        ));
    }

    /**
     * Full user directory for Super Admin. Large enough to paginate at 10/page.
     */
    public static function directory(): array
    {
        return [
            ['id' => 1, 'name' => 'Gloriose Mukandayisenga', 'email' => 'g.mukandayisenga@ororaschool.rw', 'district' => 'Kigali', 'roles' => ['ororafarm' => 'super-admin', 'gemura' => 'super-admin', 'buchapro' => 'super-admin', 'feedgrid' => 'super-admin'], 'status' => 'active', 'last_seen' => 'Just now', 'kind' => 'staff', 'created' => '03 Mar 2023'],
            ['id' => 2, 'name' => 'Jean-Baptiste Habimana', 'email' => 'jb.habimana@gemura.rw', 'district' => 'Musanze', 'roles' => ['gemura' => 'platform-admin', 'ororafarm' => 'instructor'], 'status' => 'active', 'last_seen' => '1 hour ago', 'kind' => 'staff', 'created' => '02 Jul 2023'],
            ['id' => 3, 'name' => 'Claudine Uwimana', 'email' => 'c.uwimana@buchapro.rw', 'district' => 'Gasabo', 'roles' => ['buchapro' => 'platform-admin', 'gemura' => 'content-manager'], 'status' => 'active', 'last_seen' => 'Yesterday', 'kind' => 'staff', 'created' => '19 Feb 2024'],
            ['id' => 4, 'name' => 'Aline Mukamana', 'email' => 'a.mukamana@ororafarm.rw', 'district' => 'Nyagatare', 'roles' => ['ororafarm' => 'platform-admin'], 'status' => 'active', 'last_seen' => '12 minutes ago', 'kind' => 'staff', 'created' => '11 Apr 2023'],
            ['id' => 5, 'name' => 'Emmanuel Nshimiyimana', 'email' => 'e.nshimiyimana@feedgrid.rw', 'district' => 'Rwamagana', 'roles' => ['feedgrid' => 'platform-admin'], 'status' => 'active', 'last_seen' => '3 hours ago', 'kind' => 'staff', 'created' => '30 Jun 2024'],
            ['id' => 6, 'name' => 'Solange Nyirahabimana', 'email' => 's.nyirahabimana@gemura.rw', 'district' => 'Burera', 'roles' => ['gemura' => 'content-manager'], 'status' => 'active', 'last_seen' => '2 days ago', 'kind' => 'staff', 'created' => '18 Aug 2023'],
            ['id' => 7, 'name' => 'Olivier Mugisha', 'email' => 'o.mugisha@buchapro.rw', 'district' => 'Karongi', 'roles' => ['buchapro' => 'content-manager'], 'status' => 'active', 'last_seen' => '25 minutes ago', 'kind' => 'staff', 'created' => '04 Mar 2024'],
            ['id' => 8, 'name' => 'Thierry Munyaneza', 'email' => 't.munyaneza@ororafarm.rw', 'district' => 'Kayonza', 'roles' => ['ororafarm' => 'instructor'], 'status' => 'active', 'last_seen' => '40 minutes ago', 'kind' => 'staff', 'created' => '22 May 2023'],
            ['id' => 9, 'name' => 'Diane Iradukunda', 'email' => 'd.iradukunda@feedgrid.rw', 'district' => 'Kamonyi', 'roles' => ['feedgrid' => 'instructor', 'ororafarm' => 'reviewer'], 'status' => 'active', 'last_seen' => '5 hours ago', 'kind' => 'staff', 'created' => '12 Jul 2024'],
            ['id' => 10, 'name' => 'Fabrice Gasana', 'email' => 'f.gasana@buchapro.rw', 'district' => 'Nyarugenge', 'roles' => ['buchapro' => 'instructor'], 'status' => 'draft', 'last_seen' => 'Never', 'kind' => 'staff', 'created' => '01 Sep 2026'],
            ['id' => 11, 'name' => 'Josiane Kayitesi', 'email' => 'j.kayitesi@gemura.rw', 'district' => 'Gicumbi', 'roles' => ['gemura' => 'field-coordinator'], 'status' => 'active', 'last_seen' => '8 minutes ago', 'kind' => 'staff', 'created' => '09 Oct 2023'],
            ['id' => 12, 'name' => 'Vincent Dusabimana', 'email' => 'v.dusabimana@ororafarm.rw', 'district' => 'Gatsibo', 'roles' => ['ororafarm' => 'field-coordinator'], 'status' => 'archived', 'last_seen' => '4 months ago', 'kind' => 'staff', 'created' => '14 Jun 2023'],
            ['id' => 13, 'name' => 'Espérance Twagirayezu', 'email' => 'e.twagirayezu@feedgrid.rw', 'district' => 'Ngoma', 'roles' => ['feedgrid' => 'reviewer'], 'status' => 'active', 'last_seen' => '1 day ago', 'kind' => 'staff', 'created' => '21 Aug 2024'],
            ['id' => 14, 'name' => 'Marie-Claire Uwase', 'email' => 'mc.uwase@ubworozi.rw', 'district' => 'Huye', 'roles' => ['ubworozi' => 'platform-admin'], 'status' => 'active', 'last_seen' => '3 weeks ago', 'kind' => 'staff', 'created' => '08 Jan 2025'],
            ['id' => 15, 'name' => 'Patrick Habineza', 'email' => 'p.habineza@ishyiga.rw', 'district' => 'Nyabihu', 'roles' => ['ishyiga' => 'platform-admin'], 'status' => 'pending_review', 'last_seen' => '6 days ago', 'kind' => 'staff', 'created' => '14 Mar 2026'],
            ['id' => 16, 'name' => 'Alice Mukeshimana', 'email' => 'a.mukeshimana@ororaschool.rw', 'district' => 'Kigali', 'roles' => ['feedgrid' => 'certificate-officer', 'gemura' => 'certificate-officer'], 'status' => 'active', 'last_seen' => '22 minutes ago', 'kind' => 'staff', 'created' => '11 Nov 2024'],
            ['id' => 17, 'name' => 'John Bosco Nsengimana', 'email' => 'jb.nsengimana@ororaschool.rw', 'district' => 'Rulindo', 'roles' => ['ororafarm' => 'content-manager', 'buchapro' => 'reviewer'], 'status' => 'active', 'last_seen' => '9 hours ago', 'kind' => 'staff', 'created' => '02 Feb 2025'],
            ['id' => 18, 'name' => 'Chantal Ingabire', 'email' => 'c.ingabire@gemura.rw', 'district' => 'Burera', 'roles' => ['gemura' => 'instructor'], 'status' => 'active', 'last_seen' => '2 hours ago', 'kind' => 'staff', 'created' => '17 Jan 2024'],
            ['id' => 101, 'name' => 'Placide Bizimana', 'email' => 'p.bizimana@umuhinzi.rw', 'district' => 'Gatsibo', 'roles' => ['gemura' => 'learner', 'buchapro' => 'learner', 'feedgrid' => 'learner'], 'status' => 'active', 'last_seen' => '14 minutes ago', 'kind' => 'learner', 'created' => '04 May 2024', 'cohort' => 'Kabarore dairy group', 'enrolled' => 5, 'progress' => 72],
            ['id' => 102, 'name' => 'Immaculée Nyiransabimana', 'email' => 'i.nyiransabimana@umuhinzi.rw', 'district' => 'Nyagatare', 'roles' => ['ororafarm' => 'learner'], 'status' => 'completed', 'last_seen' => '3 days ago', 'kind' => 'learner', 'created' => '19 Jun 2024', 'cohort' => 'Rwimiyaga cooperative', 'enrolled' => 2, 'progress' => 100],
            ['id' => 103, 'name' => 'Célestin Ndayisaba', 'email' => 'c.ndayisaba@umuhinzi.rw', 'district' => 'Musanze', 'roles' => ['gemura' => 'learner'], 'status' => 'active', 'last_seen' => '1 hour ago', 'kind' => 'learner', 'created' => '22 Jul 2024', 'cohort' => 'Kinigi collection centre', 'enrolled' => 3, 'progress' => 45],
            ['id' => 104, 'name' => 'Béatrice Uwamahoro', 'email' => 'b.uwamahoro@umuhinzi.rw', 'district' => 'Rwamagana', 'roles' => ['feedgrid' => 'learner'], 'status' => 'completed', 'last_seen' => '1 week ago', 'kind' => 'learner', 'created' => '08 Aug 2024', 'cohort' => 'Muhazi feed group', 'enrolled' => 3, 'progress' => 100],
            ['id' => 105, 'name' => 'Sylvestre Rwigema', 'email' => 's.rwigema@umuhinzi.rw', 'district' => 'Kayonza', 'roles' => ['ororafarm' => 'learner'], 'status' => 'archived', 'last_seen' => '5 months ago', 'kind' => 'learner', 'created' => '11 Mar 2024', 'cohort' => 'Mwiri livestock group', 'enrolled' => 1, 'progress' => 12],
            ['id' => 106, 'name' => 'Théoneste Bagabo', 'email' => 't.bagabo@umuhinzi.rw', 'district' => 'Ngoma', 'roles' => ['feedgrid' => 'learner', 'buchapro' => 'learner'], 'status' => 'active', 'last_seen' => 'Yesterday', 'kind' => 'learner', 'created' => '29 Sep 2024', 'cohort' => 'Zaza cooperative', 'enrolled' => 2, 'progress' => 31],
            ['id' => 107, 'name' => 'Sandrine Mukamana', 'email' => 's.mukamana@umuhinzi.rw', 'district' => 'Gicumbi', 'roles' => ['gemura' => 'learner'], 'status' => 'active', 'last_seen' => '6 hours ago', 'kind' => 'learner', 'created' => '15 Jan 2025', 'cohort' => 'Rukomo milk group', 'enrolled' => 2, 'progress' => 54],
            ['id' => 108, 'name' => 'Vianney Habumuremyi', 'email' => 'v.habumuremyi@umuhinzi.rw', 'district' => 'Burera', 'roles' => ['gemura' => 'learner'], 'status' => 'active', 'last_seen' => '20 minutes ago', 'kind' => 'learner', 'created' => '03 Mar 2025', 'cohort' => 'Cyanika collection centre', 'enrolled' => 3, 'progress' => 61],
            ['id' => 109, 'name' => 'Dative Nyiranzeyimana', 'email' => 'd.nyiranzeyimana@umuhinzi.rw', 'district' => 'Musanze', 'roles' => ['gemura' => 'learner'], 'status' => 'completed', 'last_seen' => '2 days ago', 'kind' => 'learner', 'created' => '19 Apr 2025', 'cohort' => 'Kinigi collection centre', 'enrolled' => 2, 'progress' => 100],
            ['id' => 110, 'name' => 'Anastase Munyaneza', 'email' => 'a.munyaneza@umuhinzi.rw', 'district' => 'Gicumbi', 'roles' => ['gemura' => 'learner'], 'status' => 'active', 'last_seen' => 'Yesterday', 'kind' => 'learner', 'created' => '08 May 2025', 'cohort' => 'Rukomo milk group', 'enrolled' => 1, 'progress' => 28],
            ['id' => 111, 'name' => 'Jeannette Mukamana', 'email' => 'j.mukamana2@umuhinzi.rw', 'district' => 'Burera', 'roles' => ['gemura' => 'learner'], 'status' => 'active', 'last_seen' => '9 hours ago', 'kind' => 'learner', 'created' => '22 Jun 2025', 'cohort' => 'Kabarore dairy group', 'enrolled' => 2, 'progress' => 47],
            ['id' => 112, 'name' => 'Félicien Nkurunziza', 'email' => 'f.nkurunziza@umuhinzi.rw', 'district' => 'Musanze', 'roles' => ['gemura' => 'learner'], 'status' => 'active', 'last_seen' => '4 hours ago', 'kind' => 'learner', 'created' => '11 Jul 2025', 'cohort' => 'Kinigi collection centre', 'enrolled' => 3, 'progress' => 81],
            ['id' => 113, 'name' => 'Consolee Uwimana', 'email' => 'c.uwimana2@umuhinzi.rw', 'district' => 'Gatsibo', 'roles' => ['gemura' => 'learner', 'ororafarm' => 'learner'], 'status' => 'active', 'last_seen' => '11 minutes ago', 'kind' => 'learner', 'created' => '02 Aug 2025', 'cohort' => 'Kabarore dairy group', 'enrolled' => 2, 'progress' => 33],
            ['id' => 114, 'name' => 'Pascal Niyonsenga', 'email' => 'p.niyonsenga@umuhinzi.rw', 'district' => 'Karongi', 'roles' => ['buchapro' => 'learner'], 'status' => 'active', 'last_seen' => '35 minutes ago', 'kind' => 'learner', 'created' => '14 Feb 2025', 'cohort' => 'Rubengera kraal group', 'enrolled' => 2, 'progress' => 58],
            ['id' => 115, 'name' => 'Alphonsine Mukarugwiza', 'email' => 'a.mukarugwiza@umuhinzi.rw', 'district' => 'Gasabo', 'roles' => ['buchapro' => 'learner'], 'status' => 'completed', 'last_seen' => '5 days ago', 'kind' => 'learner', 'created' => '03 Mar 2025', 'cohort' => 'Nyabugogo market agents', 'enrolled' => 3, 'progress' => 100],
            ['id' => 116, 'name' => 'Innocent Hakizimana', 'email' => 'i.hakizimana@umuhinzi.rw', 'district' => 'Nyarugenge', 'roles' => ['buchapro' => 'learner'], 'status' => 'active', 'last_seen' => '2 hours ago', 'kind' => 'learner', 'created' => '21 Apr 2025', 'cohort' => 'Nyabugogo market agents', 'enrolled' => 1, 'progress' => 22],
            ['id' => 117, 'name' => 'Epiphanie Nyirahabimana', 'email' => 'e.nyirahabimana@umuhinzi.rw', 'district' => 'Karongi', 'roles' => ['buchapro' => 'learner'], 'status' => 'active', 'last_seen' => 'Yesterday', 'kind' => 'learner', 'created' => '09 May 2025', 'cohort' => 'Rubengera kraal group', 'enrolled' => 2, 'progress' => 67],
            ['id' => 118, 'name' => 'Damascène Ndayambaje', 'email' => 'd.ndayambaje@umuhinzi.rw', 'district' => 'Gasabo', 'roles' => ['buchapro' => 'learner'], 'status' => 'active', 'last_seen' => '8 hours ago', 'kind' => 'learner', 'created' => '18 Jun 2025', 'cohort' => 'Kinyinya identification desk', 'enrolled' => 2, 'progress' => 41],
            ['id' => 119, 'name' => 'Speciose Mukandoli', 'email' => 's.mukandoli@umuhinzi.rw', 'district' => 'Nyarugenge', 'roles' => ['buchapro' => 'learner'], 'status' => 'archived', 'last_seen' => '3 months ago', 'kind' => 'learner', 'created' => '12 Jan 2025', 'cohort' => 'Nyabugogo market agents', 'enrolled' => 1, 'progress' => 8],
            ['id' => 120, 'name' => 'Callixte Nsengiyumva', 'email' => 'c.nsengiyumva@umuhinzi.rw', 'district' => 'Karongi', 'roles' => ['buchapro' => 'learner'], 'status' => 'completed', 'last_seen' => '1 week ago', 'kind' => 'learner', 'created' => '27 Jul 2025', 'cohort' => 'Rubengera kraal group', 'enrolled' => 3, 'progress' => 100],
            ['id' => 121, 'name' => 'Gaudence Mukarugambwa', 'email' => 'g.mukarugambwa@umuhinzi.rw', 'district' => 'Rwamagana', 'roles' => ['feedgrid' => 'learner'], 'status' => 'active', 'last_seen' => '3 hours ago', 'kind' => 'learner', 'created' => '04 Feb 2025', 'cohort' => 'Muhazi feed group', 'enrolled' => 2, 'progress' => 49],
            ['id' => 122, 'name' => 'Straton Habimana', 'email' => 's.habimana@umuhinzi.rw', 'district' => 'Nyagatare', 'roles' => ['ororafarm' => 'learner'], 'status' => 'active', 'last_seen' => '55 minutes ago', 'kind' => 'learner', 'created' => '16 Mar 2025', 'cohort' => 'Rwimiyaga cooperative', 'enrolled' => 2, 'progress' => 36],
        ];
    }

    public static function find(int $id): ?array
    {
        foreach (self::directory() as $person) {
            if ($person['id'] === $id) {
                return $person;
            }
        }

        return null;
    }

    public static function learners(): array
    {
        return array_values(array_filter(
            self::directory(),
            fn (array $person) => ($person['kind'] ?? '') === 'learner',
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function learnersOn(string $platform): array
    {
        return array_values(array_filter(
            self::learners(),
            fn (array $person) => array_key_exists($platform, $person['roles'] ?? []),
        ));
    }

    /**
     * Staff who teach or hold the instructor role on this platform.
     *
     * @return list<array<string, mixed>>
     */
    public static function instructorsOn(string $platform): array
    {
        $names = array_unique(array_column(Courses::forPlatform($platform), 'instructor'));

        return array_values(array_filter(
            self::staff(),
            function (array $person) use ($platform, $names) {
                $role = $person['roles'][$platform] ?? null;

                return $role === 'instructor' || in_array($person['name'], $names, true);
            },
        ));
    }

    public static function districts(): array
    {
        return [
            'Nyagatare', 'Gatsibo', 'Kayonza', 'Rwamagana', 'Ngoma',
            'Musanze', 'Burera', 'Gicumbi', 'Rulindo', 'Nyabihu',
            'Rubavu', 'Karongi', 'Huye', 'Nyamagabe', 'Kamonyi', 'Kigali',
        ];
    }
}
