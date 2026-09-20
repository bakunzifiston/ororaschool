<x-layouts.platform-workspace :title="$page['course']['title']" :platform="$platformSlug">
    @php
        $status = $page['course']['status'];
        $builderPrimary = in_array($status, ['draft', 'published'], true);
        $headerTransition = null;
        if (! $builderPrimary) {
            foreach ($page['transitions'] as $transition) {
                if ($transition['variant'] === 'primary' || $status === 'archived') {
                    $headerTransition = $transition;
                    break;
                }
            }
        }
    @endphp
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="secondary" :href="route('workspace.courses.edit', ['platform' => $platformSlug, 'course' => $page['course']['slug']])">Edit course</x-button>
            @if ($builderPrimary)
                <x-button :href="route('workspace.modules', ['platform' => $platformSlug, 'course' => $page['course']['slug']])">Open builder</x-button>
            @else
                <x-button variant="ghost" :href="route('workspace.modules', ['platform' => $platformSlug, 'course' => $page['course']['slug']])">Open builder</x-button>
            @endif
            @if ($headerTransition)
                <form method="POST" action="{{ route('workspace.courses.transition', ['platform' => $platformSlug, 'course' => $page['course']['slug']]) }}">
                    @csrf
                    <input type="hidden" name="to" value="{{ $headerTransition['to'] }}">
                    <x-button type="submit">{{ $headerTransition['label'] }}</x-button>
                </form>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-panel title="Publishing workflow"
                 subtitle="Draft → Pending review → Approved → Published → Archived. Buttons match the current status.">
            <x-workflow-trail :steps="$page['steps']" :current="$page['course']['status']" />

            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($page['transitions'] as $transition)
                    @continue($headerTransition && $transition['to'] === $headerTransition['to'])
                    <form method="POST" action="{{ route('workspace.courses.transition', ['platform' => $platformSlug, 'course' => $page['course']['slug']]) }}">
                        @csrf
                        <input type="hidden" name="to" value="{{ $transition['to'] }}">
                        <x-button type="submit" :variant="$transition['variant'] === 'primary' ? 'secondary' : $transition['variant']">{{ $transition['label'] }}</x-button>
                    </form>
                @endforeach
            </div>
        </x-panel>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-panel title="Catalogue" class="lg:col-span-2">
            <dl class="grid gap-3 text-dense sm:grid-cols-2">
                <div>
                    <dt class="text-micro text-fern-500">Instructor</dt>
                    <dd class="mt-1">{{ $page['course']['instructor'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Difficulty</dt>
                    <dd class="mt-1">{{ $page['course']['difficulty'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Duration</dt>
                    <dd class="mt-1 figure">{{ $page['course']['duration'] }} minutes</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Language</dt>
                    <dd class="mt-1">{{ $page['course']['language'] }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Enrolments</dt>
                    <dd class="mt-1 figure">{{ number_format($page['course']['enrolled']) }}</dd>
                </div>
                <div>
                    <dt class="text-micro text-fern-500">Certificate</dt>
                    <dd class="mt-1">{{ $page['course']['certificate_eligible'] ? 'Eligible' : 'Not eligible' }}</dd>
                </div>
            </dl>
            <p class="mt-4 text-dense leading-relaxed text-fern-500">{{ $page['course']['description'] }}</p>
        </x-panel>

        <x-panel title="Status">
            <x-status-badge :status="$page['course']['status']" />
            <p class="mt-3 text-micro text-fern-500">Updated {{ $page['course']['updated'] }}</p>
            <p class="mt-1 text-micro text-fern-500">{{ $page['course']['paid'] ? 'Paid' : 'Free' }} · enrolment {{ $page['course']['enrollment_required'] ? 'required' : 'open' }}</p>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
