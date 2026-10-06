<x-layouts.platform-workspace title="Lessons" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.lessons', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
            <x-button :href="route('workspace.modules', ['platform' => $platformSlug])">Open builder</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'title', 'label' => 'Lesson'],
                                ['key' => 'course', 'label' => 'Course'],
                                ['key' => 'module', 'label' => 'Module'],
                                ['key' => 'type', 'label' => 'Type'],
                                ['key' => 'duration', 'label' => 'Mins', 'align' => 'right'],
                                ['key' => 'actions', 'label' => '', 'align' => 'right'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="52rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-accent-50/60">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('workspace.modules', ['platform' => $platformSlug, 'course' => $row['course_slug']]) }}"
                                   class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['title'] }}</a>
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['course'] }}</td>
                            <td class="px-3 py-2.5 text-dense text-fern-500">{{ $row['module'] }}</td>
                            <td class="px-3 py-2.5"><x-content-type :type="$row['type']" /></td>
                            <td class="figure px-3 py-2.5 text-right text-micro text-fern-500">{{ $row['duration'] }}</td>
                            <td class="px-3 py-2.5 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <x-button variant="ghost" size="sm"
                                              :href="route('workspace.modules', ['platform' => $platformSlug, 'course' => $row['course_slug']])">
                                        View
                                    </x-button>
                                    <x-button variant="ghost" size="sm"
                                              x-on:click="$dispatch('open-modal', 'edit-lesson-{{ $row['id'] }}')">
                                        Edit
                                    </x-button>
                                    <x-button variant="danger" size="sm"
                                              x-on:click="$dispatch('open-modal', 'delete-lesson-{{ $row['id'] }}')">
                                        Delete
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>

    @foreach ($page['rows'] as $row)
        <x-modal :name="'edit-lesson-'.$row['id']" width="md"
                 :title="'Edit '.$row['title']"
                 subtitle="Lesson under {{ $row['module'] }}.">
            <form method="POST" action="{{ route('workspace.lessons.update', ['platform' => $platformSlug, 'lesson' => $row['id']]) }}" enctype="multipart/form-data" class="grid gap-4">
                @csrf
                <input type="hidden" name="course" value="{{ $row['course_slug'] }}">
                <input type="hidden" name="return" value="lessons">
                @include('workspace.curriculum.partials.lesson-fields', ['types' => $page['types'], 'lesson' => $row])
                <div class="flex justify-end gap-2">
                    <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                    <x-button type="submit">Save lesson</x-button>
                </div>
            </form>
        </x-modal>

        <x-modal :name="'delete-lesson-'.$row['id']" width="md"
                 :title="'Delete '.$row['title'].'?'"
                 subtitle="Lesson">
            <p>{{ $row['title'] }} will leave {{ $row['module'] }}.</p>
            <x-slot:actions>
                <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                <form method="POST" action="{{ route('workspace.lessons.destroy', ['platform' => $platformSlug, 'lesson' => $row['id']]) }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="course" value="{{ $row['course_slug'] }}">
                    <input type="hidden" name="return" value="lessons">
                    <x-button type="submit" variant="danger">Delete lesson</x-button>
                </form>
            </x-slot:actions>
        </x-modal>
    @endforeach
</x-layouts.platform-workspace>
