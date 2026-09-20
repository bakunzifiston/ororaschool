<x-layouts.platform-workspace :title="$page['header']['title']" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST"
          action="{{ $page['isEdit']
              ? route('workspace.courses.update', ['platform' => $platformSlug, 'course' => $page['course']['slug']])
              : route('workspace.courses.store', ['platform' => $platformSlug]) }}"
          class="mt-6 max-w-3xl">
        @csrf

        <x-panel title="Catalogue record">
            <div class="grid gap-5">
                <x-field name="title" label="Title" size="sm"
                         :value="$page['course']['title']"
                         placeholder="e.g. Mastitis Detection and Milk Hygiene" />

                <x-textarea name="description" label="Description"
                            :value="$page['course']['description'] ?? $page['course']['summary'] ?? ''"
                            hint="Shown on the learner catalogue and the public certificate." />

                <div>
                    <p class="mb-1.5 text-dense font-medium text-basalt-800">Thumbnail</p>
                    <div class="flex items-center gap-3 rounded-md border border-dashed border-clay-300 bg-papyrus px-4 py-5">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-md border border-clay-200 bg-chalk text-fern-500">
                            <x-icon name="upload" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0">
                            <p class="text-dense text-basalt-800">Drop a 16:9 still, or choose a file</p>
                            <p class="mt-0.5 text-micro text-fern-500">JPG or PNG. Upload is a placeholder in this build.</p>
                        </div>
                    </div>
                    <input type="file" name="thumbnail" accept="image/jpeg,image/png" class="sr-only" aria-label="Course thumbnail">
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <x-select name="academy" label="Academy" size="sm"
                              :options="$page['academies']"
                              :selected="$page['course']['academy_slug'] ?? ''"
                              placeholder="Choose an academy" />
                    <x-field name="category" label="Category" size="sm"
                             :value="$page['course']['category'] ?? ''"
                             placeholder="e.g. Milking routine" />
                </div>

                <fieldset>
                    <legend class="mb-1.5 text-dense font-medium text-basalt-800">Instructor(s)</legend>
                    <div class="grid gap-2 rounded-md border border-clay-200 bg-papyrus px-3 py-3">
                        @forelse ($page['instructors'] as $name)
                            <label class="flex items-start gap-2.5 text-dense text-basalt-800">
                                <input type="checkbox" name="instructors[]" value="{{ $name }}"
                                       @checked(in_array($name, $page['course']['instructors'] ?? array_filter([$page['course']['instructor'] ?? '']), true))
                                       class="mt-0.5 h-4 w-4 shrink-0 rounded-xs border-clay-300 bg-chalk text-accent-500 accent-[var(--color-accent-500)]">
                                <span>{{ $name }}</span>
                            </label>
                        @empty
                            <p class="text-micro text-fern-500">No instructors on this platform yet.</p>
                        @endforelse
                    </div>
                    <p class="mt-1.5 text-micro leading-relaxed text-fern-500">Tick everyone who teaches this course. The first ticked name is the lead on the catalogue card.</p>
                </fieldset>

                <div class="grid gap-5 sm:grid-cols-3">
                    <x-select name="difficulty" label="Difficulty" size="sm"
                              :options="$page['difficulties']"
                              :selected="$page['course']['difficulty'] ?? 'Foundation'" />
                    <x-field name="duration" label="Duration (minutes)" size="sm"
                             :value="(string) ($page['course']['duration'] ?? 60)" />
                    <x-select name="language" label="Language" size="sm"
                              :options="$page['languages']"
                              :selected="$page['course']['language'] ?? 'English'" />
                </div>

                <x-toggle name="paid" label="Paid course"
                          :checked="(bool) ($page['course']['paid'] ?? false)"
                          hint="Free courses appear on the open catalogue. Paid ones need an enrolment." />

                <x-toggle name="certificate_eligible" label="Certificate-eligible"
                          :checked="(bool) ($page['course']['certificate_eligible'] ?? true)"
                          hint="When on, finishing the course mints a number against the global format." />

                <x-toggle name="enrollment_required" label="Enrolment required"
                          :checked="(bool) ($page['course']['enrollment_required'] ?? true)"
                          hint="When off, any signed-in learner on this platform can open it." />
            </div>
        </x-panel>

        <div class="mt-4 flex flex-wrap gap-2">
            <x-button type="submit">{{ $page['isEdit'] ? 'Save course' : 'Create draft' }}</x-button>
            <x-button variant="secondary" :href="route('workspace.courses', ['platform' => $platformSlug])">Cancel</x-button>
        </div>
    </form>
</x-layouts.platform-workspace>
