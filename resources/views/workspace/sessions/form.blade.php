<x-layouts.platform-workspace :title="$page['header']['title']" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST" action="{{ $page['isEdit'] ? route('workspace.sessions.update', ['platform' => $platformSlug, 'session' => $page['session']['id']]) : route('workspace.sessions.store', ['platform' => $platformSlug]) }}"
          class="mt-6 max-w-3xl">
        @csrf

        <x-panel title="Session">
            <div class="grid gap-5">
                <x-field name="title" label="Title" size="sm"
                         :value="$page['session']['title'] ?? ''" required />

                <x-select name="course" label="Course" size="sm"
                          :options="$page['courses']"
                          :selected="$page['session']['course_slug'] ?? ''"
                          placeholder="Attach to a course" />

                <x-select name="instructor" label="Instructor" size="sm"
                          :options="$page['instructors']"
                          :selected="$page['session']['instructor'] ?? ''"
                          placeholder="Who is teaching" />

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-field name="starts_at" label="Scheduled date / time" size="sm"
                             :value="$page['session']['starts'] ?? ''"
                             placeholder="Tue 22 Sep, 09:00" />
                    <x-field name="duration" label="Duration (minutes)" size="sm"
                             :value="(string) ($page['session']['duration'] ?? 45)" />
                </div>

                <x-field name="meeting_url" label="Meeting URL" size="sm"
                         :value="$page['session']['url'] ?? ''"
                         placeholder="https://meet.ororaschool.rw/…" />

                @if (($page['session']['status'] ?? '') === 'completed' || $page['isEdit'])
                    <x-field name="recording_url" label="Recording URL" size="sm"
                             :value="$page['session']['recording'] ?? ''"
                             placeholder="https://recordings.ororaschool.rw/…"
                             hint="Past sessions keep a recording link for learners who missed the call." />
                @endif
            </div>
        </x-panel>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-button type="submit">{{ $page['isEdit'] ? 'Save session' : 'Schedule session' }}</x-button>
            <x-button variant="secondary" :href="route('workspace.sessions', ['platform' => $platformSlug])">Cancel</x-button>
        </div>
    </form>
</x-layouts.platform-workspace>
