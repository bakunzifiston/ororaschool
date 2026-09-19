<x-layouts.platform-workspace :title="$page['quiz']['title']" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <form method="POST" action="{{ route('workspace.quizzes.update', ['platform' => $platformSlug, 'quiz' => $page['quiz']['slug']]) }}" class="mt-6">
        @csrf

        <x-panel title="Quiz settings" class="max-w-2xl">
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field name="pass" label="Passing score (%)" size="sm" :value="(string) $page['quiz']['pass']" />
                <x-field name="attempts" label="Attempt limit" size="sm" :value="(string) $page['quiz']['attempts']" />
            </div>
        </x-panel>

        <div class="mt-6">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <h2 class="font-display text-section text-basalt-900">Questions</h2>
                <x-button type="button" variant="secondary" size="sm" icon="plus">Add question</x-button>
            </div>

            <ol class="grid gap-3">
                @foreach ($page['quiz']['items'] as $index => $item)
                    <li class="rounded-md border border-clay-200 bg-chalk px-4 py-3">
                        <p class="text-dense font-medium text-basalt-900">
                            <span class="figure text-micro text-fern-400">{{ $index + 1 }}.</span>
                            {{ $item['prompt'] }}
                        </p>
                        <ul class="mt-3 grid gap-1.5">
                            @foreach ($item['options'] as $optionIndex => $option)
                                <li @class([
                                    'flex items-start gap-2 rounded-sm px-2 py-1.5 text-dense',
                                    'border border-st-active bg-accent-50 text-basalt-900' => $optionIndex === $item['correct'],
                                    'border border-transparent text-fern-600' => $optionIndex !== $item['correct'],
                                ])>
                                    <span class="mt-0.5 inline-flex h-4 w-4 shrink-0 items-center justify-center rounded-full border {{ $optionIndex === $item['correct'] ? 'border-st-active bg-st-active text-white' : 'border-clay-300' }}">
                                        @if ($optionIndex === $item['correct'])
                                            <x-icon name="check" class="h-2.5 w-2.5" />
                                        @endif
                                    </span>
                                    <span>{{ $option }}</span>
                                    @if ($optionIndex === $item['correct'])
                                        <span class="ml-auto text-micro text-fern-500">Correct</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="mt-6">
            <x-button type="submit">Save quiz</x-button>
            <x-button variant="secondary" :href="route('workspace.quizzes', ['platform' => $platformSlug])">Back to quizzes</x-button>
        </div>
    </form>
</x-layouts.platform-workspace>
