<x-layouts.platform-workspace title="Quizzes" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.quizzes', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
            <form method="POST" action="{{ route('workspace.quizzes.store', ['platform' => $platformSlug]) }}">
                @csrf
                <x-button type="submit" icon="plus">New quiz</x-button>
            </form>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('workspace.quizzes', ['platform' => $platformSlug]) }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="min-w-72 grow">
            <x-select name="course" label="Course" size="sm"
                      :options="$page['filters']['courses']"
                      :selected="$page['filters']['course']" />
        </div>
        <x-button type="submit" variant="secondary" size="sm">Filter</x-button>
    </form>

    <div class="mt-4">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'title', 'label' => 'Quiz'],
                                ['key' => 'course', 'label' => 'Course'],
                                ['key' => 'lesson', 'label' => 'Lesson'],
                                ['key' => 'questions', 'label' => 'Questions', 'align' => 'right'],
                                ['key' => 'pass', 'label' => 'Pass', 'align' => 'right'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="52rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('workspace.quizzes.show', ['platform' => $platformSlug, 'quiz' => $row['slug']]) }}"
                                   class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['title'] }}</a>
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['course'] }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['lesson'] }}</td>
                            <td class="figure px-3 py-2.5 text-right text-micro">{{ $row['questions'] }}</td>
                            <td class="figure px-3 py-2.5 text-right text-micro">{{ $row['pass'] }}%</td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
