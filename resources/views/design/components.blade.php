@php
    // The same page, rendered inside whichever layout is requested — which is
    // how the per-experience accent variation gets compared directly.
    $layout = 'layouts.' . $experience;

    $experienceLabels = [
        'super-admin' => 'Super Admin',
        'platform-workspace' => 'Platform Workspace',
        'learner' => 'Learner',
    ];
@endphp

<x-dynamic-component :component="$layout" title="Component proof">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            @foreach ($experienceLabels as $key => $label)
                <x-button size="sm" :variant="$key === $experience ? 'primary' : 'secondary'"
                          :href="route('design.components', ['experience' => $key])">
                    {{ $label }}
                </x-button>
            @endforeach
        </x-slot:actions>
    </x-page-header>

    <div class="mt-8 grid grid-cols-1 gap-10">

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Stat card</h2>
            <p class="mt-1 text-dense text-fern-500">Label, value, optional trend with direction, optional note.</p>

            @foreach (['a', 'b'] as $set)
                <p class="mt-5 mb-2 text-micro text-fern-500">{{ $page['stats'][$set]['caption'] }}</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($page['stats'][$set]['items'] as $stat)
                        <x-stat-card :label="$stat['label']" :value="$stat['value']"
                                     :trend="$stat['trend']" :direction="$stat['direction']" :note="$stat['note']" />
                    @endforeach
                </div>
            @endforeach
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Status badge</h2>
            <p class="mt-1 text-dense text-fern-500">
                Each state has its own hue and its own treatment — dashed outline, tint, drawn outline,
                solid fill — so they stay apart in greyscale. An unmapped value falls back rather than breaking.
            </p>

            @foreach (['a', 'b'] as $set)
                <p class="mt-5 mb-2 text-micro text-fern-500">{{ $page['statuses'][$set]['caption'] }}</p>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($page['statuses'][$set]['items'] as $status)
                        <x-status-badge :status="$status" />
                    @endforeach
                </div>
            @endforeach
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Progress bar</h2>
            <p class="mt-1 text-dense text-fern-500">Accent while in progress, completed gold at 100%.</p>

            <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach (['a', 'b'] as $set)
                    <div class="min-w-0">
                        <p class="mb-3 text-micro text-fern-500">{{ $page['progress'][$set]['caption'] }}</p>
                        <div class="grid gap-4">
                            @foreach ($page['progress'][$set]['items'] as $bar)
                                <x-progress-bar :value="$bar['value']" :label="$bar['label']" :meta="$bar['meta']"
                                                :size="$set === 'b' ? 'sm' : 'md'" />
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Role chip</h2>
            <p class="mt-1 text-dense text-fern-500">
                Elevated roles carry a shield and a basalt fill; scope and a remove control are optional.
            </p>

            @foreach (['a', 'b'] as $set)
                <p class="mt-5 mb-2 text-micro text-fern-500">{{ $page['roles'][$set]['caption'] }}</p>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($page['roles'][$set]['items'] as $role)
                        <x-role-chip :role="$role"
                                     :show-scope="$page['roles'][$set]['scope']"
                                     :removable="$page['roles'][$set]['removable']" />
                    @endforeach
                </div>
            @endforeach
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Course card</h2>
            <p class="mt-1 text-dense text-fern-500">
                Generated thumbnail keyed to the platform's discipline. Pass a progress value and the footer
                becomes a progress bar; leave it out and it shows the instructor and length.
            </p>

            @foreach (['a', 'b'] as $set)
                <p class="mt-5 mb-2 text-micro text-fern-500">{{ $page['courseCards'][$set]['caption'] }}</p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($page['courseCards'][$set]['items'] as $item)
                        <x-course-card :course="$item['course']"
                                       :platform-label="$item['platform']"
                                       :progress="$item['progress']"
                                       :status="$item['status'] ?? null" />
                    @endforeach
                </div>
            @endforeach
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Data table</h2>
            <p class="mt-1 text-dense text-fern-500">
                Columns and rows are props. Cell types (person, status, role, progress) are declared on the
                column, so no page reimplements a cell. Pagination is a slot-in component.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-6">
                @foreach (['a', 'b', 'c'] as $set)
                    <div class="min-w-0">
                        <p class="mb-2 text-micro text-fern-500">{{ $page['tables'][$set]['caption'] }}</p>
                        <x-panel variant="table" :padded="false">
                            <x-data-table :columns="$page['tables'][$set]['columns']"
                                          :rows="$page['tables'][$set]['rows']"
                                          :pagination="$page['tables'][$set]['pagination']"
                                          min-width="34rem"
                                          empty-title="No learners on this cohort yet"
                                          empty-message="Enrol a cohort from the learner register and they will show up here with their progress." />
                        </x-panel>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Empty state</h2>
            <p class="mt-1 text-dense text-fern-500">
                Copy is supplied by the page, in the voice of whoever is reading it — staff get the
                scheduling instruction, learners get an encouragement and a next step.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-2">
                @foreach (['a', 'b'] as $set)
                    <x-panel :padded="false">
                        <x-empty-state :icon="$page['emptyStates'][$set]['icon']"
                                       :title="$page['emptyStates'][$set]['title']"
                                       :message="$page['emptyStates'][$set]['message']">
                            <x-slot:actions>
                                <x-button variant="secondary" size="sm">{{ $page['emptyStates'][$set]['action'] }}</x-button>
                            </x-slot:actions>
                        </x-empty-state>
                    </x-panel>
                @endforeach
            </div>
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Page header</h2>
            <p class="mt-1 text-dense text-fern-500">
                Breadcrumb, title, optional subtitle, and a slot for one primary action plus secondaries.
                The header at the top of this page is the same component with a three-level breadcrumb.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-6">
                <x-panel>
                    <x-page-header :breadcrumb="[
                            ['label' => 'Gemura', 'route' => 'workspace.dashboard', 'params' => ['platform' => 'gemura']],
                            ['label' => 'Courses'],
                        ]"
                        title="Courses"
                        subtitle="Eleven courses across dairy hygiene, collection-centre practice and herd fertility."
                        class="border-b-0 pb-0">
                        <x-slot:actions>
                            <x-button variant="secondary" size="sm" icon="file">Export</x-button>
                            <x-button size="sm" icon="plus">New course</x-button>
                        </x-slot:actions>
                    </x-page-header>
                </x-panel>

                <x-panel>
                    <x-page-header :breadcrumb="[['label' => 'Orora School', 'route' => 'learner.dashboard'], ['label' => 'My certificates']]"
                                   title="My certificates"
                                   class="border-b-0 pb-0" />
                </x-panel>
            </div>
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Modal shell</h2>
            <p class="mt-1 text-dense text-fern-500">
                One shell for confirm, create and edit flows. Opened by dispatching
                <code class="figure rounded-xs bg-clay-100 px-1 text-micro">open-modal</code> with the dialog's name;
                closes on the overlay, the close control or Escape. This is the one place motion is used.
            </p>

            <div class="mt-5 flex flex-wrap gap-2">
                @foreach (['a', 'b'] as $set)
                    <x-button :variant="$page['modals'][$set]['variant'] === 'danger' ? 'danger' : 'secondary'"
                              x-on:click="$dispatch('open-modal', '{{ $page['modals'][$set]['name'] }}')">
                        {{ $page['modals'][$set]['trigger'] }}
                    </x-button>
                @endforeach
            </div>

            @foreach (['a', 'b'] as $set)
                @php $modal = $page['modals'][$set]; @endphp

                <x-modal :name="$modal['name']" :title="$modal['title']" :subtitle="$modal['subtitle']"
                         :width="$set === 'a' ? 'md' : 'xl'">
                    <p>{{ $modal['body'] }}</p>

                    @if ($set === 'b')
                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <label class="grid gap-1">
                                <span class="text-micro text-fern-500">Full name</span>
                                <input type="text" placeholder="e.g. Josiane Kayitesi"
                                       class="h-9 rounded-md border border-clay-200 bg-chalk px-2.5 text-dense text-basalt-800 placeholder:text-clay-300">
                            </label>
                            <label class="grid gap-1">
                                <span class="text-micro text-fern-500">Email</span>
                                <input type="email" placeholder="name@gemura.rw"
                                       class="h-9 rounded-md border border-clay-200 bg-chalk px-2.5 text-dense text-basalt-800 placeholder:text-clay-300">
                            </label>
                        </div>
                    @endif

                    <x-slot:actions>
                        <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                        <x-button :variant="$modal['variant']" x-on:click="open = false">{{ $modal['confirm'] }}</x-button>
                    </x-slot:actions>
                </x-modal>
            @endforeach
        </section>

        {{-- ---------------------------------------------------------------- --}}
        <section class="min-w-0">
            <h2 class="font-display text-section text-basalt-900">Supporting primitives</h2>
            <p class="mt-1 text-dense text-fern-500">
                Built because the nine above needed them, and reused by pages directly.
            </p>

            <div class="mt-5 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <x-panel title="Button" subtitle="Four variants, three sizes">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-button>Primary</x-button>
                        <x-button variant="secondary">Secondary</x-button>
                        <x-button variant="ghost">Ghost</x-button>
                        <x-button variant="danger">Danger</x-button>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <x-button size="sm" icon="plus">Small</x-button>
                        <x-button size="md" icon-after="arrow-right">Medium</x-button>
                        <x-button size="lg" icon="play">Large</x-button>
                        <x-button disabled>Disabled</x-button>
                    </div>
                </x-panel>

                <x-panel title="Panel and avatar" subtitle="Three surface treatments by function">
                    <div class="grid gap-3">
                        <x-panel variant="plain" class="!bg-papyrus">
                            <p class="text-dense text-fern-500">Plain — editorial content and forms.</p>
                        </x-panel>
                        <x-panel variant="quiet">
                            <p class="text-dense text-fern-500">Quiet — secondary asides and placeholders.</p>
                        </x-panel>
                        <div class="flex items-center gap-2">
                            <x-avatar name="Espérance Twagirayezu" size="sm" />
                            <x-avatar name="Jean-Baptiste Habimana" size="md" />
                            <x-avatar name="Placide Bizimana" size="lg" />
                        </div>
                    </div>
                </x-panel>
            </div>
        </section>
    </div>
</x-dynamic-component>
