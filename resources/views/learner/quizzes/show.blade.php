<x-layouts.learner :title="$page['quiz']['title']" width="read">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']" />

    <p class="mt-4 text-micro text-fern-500">Single-page quiz — every question on one screen.</p>

    @if ($page['result'])
        <div class="mt-6 rounded-md border border-clay-200 {{ $page['result']['passed'] ? 'border-l-2 border-l-st-completed' : 'border-l-2 border-l-st-pending' }} bg-chalk px-5 py-5">
            <p class="text-micro text-fern-500">{{ $page['result']['label'] }}</p>
            <p class="mt-1 font-display text-title text-basalt-900">{{ $page['result']['score'] }}%</p>
            <p class="mt-1 text-dense text-fern-600">
                Passing score is {{ $page['quiz']['pass'] }}%. Attempt limit {{ $page['quiz']['attempts'] }}.
                This result is a fixture — nothing was stored.
            </p>
            <div class="mt-4">
                <x-button :href="route('learner.courses.show', ['course' => $page['quiz']['course_slug']])">Back to the course</x-button>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('learner.quizzes.submit', ['quiz' => $page['quiz']['slug']]) }}" class="mt-6 grid gap-4">
            @csrf

            <ol class="grid gap-4">
                @foreach ($page['quiz']['items'] as $index => $item)
                    <li class="rounded-md border border-clay-200 bg-chalk px-4 py-4">
                        <p class="text-dense font-medium text-basalt-900">
                            <span class="figure text-micro text-fern-500">{{ $index + 1 }}.</span>
                            {{ $item['prompt'] }}
                        </p>
                        <ul class="mt-3 grid gap-2">
                            @foreach ($item['options'] as $optionIndex => $option)
                                <li>
                                    <label class="flex items-start gap-2.5 rounded-sm px-1 py-1 text-dense text-basalt-800">
                                        <input type="radio" name="answers[{{ $index }}]" value="{{ $optionIndex }}"
                                               class="mt-0.5 h-4 w-4 shrink-0 border-clay-300 accent-[var(--color-accent-500)]">
                                        <span>{{ $option }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ol>

            <div>
                <x-button type="submit">Submit quiz</x-button>
            </div>
        </form>
    @endif
</x-layouts.learner>
