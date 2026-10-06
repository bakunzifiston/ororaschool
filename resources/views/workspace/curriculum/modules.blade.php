<x-layouts.platform-workspace title="Modules" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm"
                      :href="route('workspace.modules', ['platform' => $platformSlug, 'course' => $page['course']['slug'] ?? null, 'empty' => 1])">
                Preview empty
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('workspace.modules', ['platform' => $platformSlug]) }}" class="app-filters">
        <div class="min-w-72 grow">
            <x-select name="course" label="Course" size="sm" :autosubmit="true"
                      :options="$page['courses']"
                      :selected="$page['course']['slug'] ?? ''" />
        </div>
    </form>

    @if ($page['course'])
        <div class="mt-4 flex flex-wrap gap-2">
            <x-button icon="plus" x-on:click="$dispatch('open-modal', 'add-module')">Add module</x-button>
        </div>
    @endif

    <div class="mt-4">
        @if (! count($page['modules']))
            <x-panel :padded="false">
                <x-empty-state icon="layers"
                               :title="$page['emptyTitle']"
                               :message="$page['emptyMessage']" />
            </x-panel>
        @else
            <ol class="grid gap-3">
                @foreach ($page['modules'] as $module)
                    <li class="rounded-md border border-clay-200 bg-chalk">
                        <div class="flex flex-wrap items-center gap-2 border-b border-clay-200 bg-papyrus px-3 py-2.5">
                            <span class="figure text-micro text-fern-500">{{ $module['order'] }}</span>
                            <h2 class="min-w-0 grow font-display text-panel font-semibold text-basalt-900">{{ $module['title'] }}</h2>
                            <x-button variant="ghost" size="sm" icon="plus"
                                      x-on:click="$dispatch('open-modal', 'add-lesson-{{ $module['id'] }}')">
                                Add lesson
                            </x-button>
                            <x-button variant="ghost" size="sm"
                                      x-on:click="$dispatch('open-modal', 'edit-module-{{ $module['id'] }}')">
                                Edit
                            </x-button>
                            <x-button variant="danger" size="sm"
                                      x-on:click="$dispatch('open-modal', 'delete-module-{{ $module['id'] }}')">
                                Delete
                            </x-button>
                        </div>
                        <ul class="divide-y divide-clay-100">
                            @foreach ($module['lessons'] as $lesson)
                                <li class="flex flex-wrap items-center gap-3 px-3 py-2.5">
                                    <span class="figure text-micro text-fern-500">{{ $module['order'] }}.{{ $loop->iteration }}</span>
                                    <p class="min-w-0 grow text-dense text-basalt-800">{{ $lesson['title'] }}</p>
                                    <x-content-type :type="$lesson['type']" />
                                    <span class="figure text-micro text-fern-500">{{ $lesson['duration'] }}m</span>
                                    <x-button variant="ghost" size="sm"
                                              x-on:click="$dispatch('open-modal', 'edit-lesson-{{ $lesson['id'] }}')">
                                        Edit
                                    </x-button>
                                    <x-button variant="danger" size="sm"
                                              x-on:click="$dispatch('open-modal', 'delete-lesson-{{ $lesson['id'] }}')">
                                        Delete
                                    </x-button>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    @if ($page['course'])
        <x-modal name="add-module" width="md"
                 :title="'Add a module to '.$page['course']['title']"
                 subtitle="The new module sits at the end of this course.">
            <form method="POST" action="{{ route('workspace.modules.store', ['platform' => $platformSlug]) }}" class="grid gap-4">
                @csrf
                <input type="hidden" name="course" value="{{ $page['course']['slug'] }}">
                <x-field name="title" label="Module name" size="sm" required />
                <div class="flex justify-end gap-2">
                    <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                    <x-button type="submit" icon="plus">Add module</x-button>
                </div>
            </form>
        </x-modal>

        @foreach ($page['modules'] as $module)
            <x-modal :name="'add-lesson-'.$module['id']" width="md"
                     :title="'Add a lesson to '.$module['title']"
                     subtitle="The new lesson sits under this module.">
                <form method="POST" action="{{ route('workspace.lessons.store', ['platform' => $platformSlug]) }}" enctype="multipart/form-data" class="grid gap-4">
                    @csrf
                    <input type="hidden" name="course" value="{{ $page['course']['slug'] }}">
                    <input type="hidden" name="module" value="{{ $module['id'] }}">
                    @include('workspace.curriculum.partials.lesson-fields', [
                        'types' => $page['types'],
                        'fieldSuffix' => 'add-'.$module['id'],
                    ])
                    <div class="flex justify-end gap-2">
                        <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                        <x-button type="submit">Add lesson</x-button>
                    </div>
                </form>
            </x-modal>

            <x-modal :name="'edit-module-'.$module['id']" width="md"
                     :title="'Edit '.$module['title']"
                     subtitle="Module name on this course.">
                <form method="POST" action="{{ route('workspace.modules.update', ['platform' => $platformSlug, 'module' => $module['id']]) }}" class="grid gap-4">
                    @csrf
                    <input type="hidden" name="course" value="{{ $page['course']['slug'] }}">
                    <x-field name="title" label="Module name" size="sm" :value="$module['title']" required />
                    <div class="flex justify-end gap-2">
                        <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                        <x-button type="submit">Save module</x-button>
                    </div>
                </form>
            </x-modal>

            <x-modal :name="'delete-module-'.$module['id']" width="md"
                     :title="'Delete '.$module['title'].'?'"
                     subtitle="Module">
                <p>{{ $module['title'] }} will leave this course, along with any lessons under it.</p>
                <x-slot:actions>
                    <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                    <form method="POST" action="{{ route('workspace.modules.destroy', ['platform' => $platformSlug, 'module' => $module['id']]) }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="course" value="{{ $page['course']['slug'] }}">
                        <x-button type="submit" variant="danger">Delete module</x-button>
                    </form>
                </x-slot:actions>
            </x-modal>

            @foreach ($module['lessons'] as $lesson)
                <x-modal :name="'edit-lesson-'.$lesson['id']" width="md"
                         :title="'Edit '.$lesson['title']"
                         subtitle="Lesson under {{ $module['title'] }}.">
                <form method="POST" action="{{ route('workspace.lessons.update', ['platform' => $platformSlug, 'lesson' => $lesson['id']]) }}" enctype="multipart/form-data" class="grid gap-4">
                    @csrf
                    <input type="hidden" name="course" value="{{ $page['course']['slug'] }}">
                    @include('workspace.curriculum.partials.lesson-fields', ['types' => $page['types'], 'lesson' => $lesson])
                    <div class="flex justify-end gap-2">
                        <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                        <x-button type="submit">Save lesson</x-button>
                    </div>
                </form>
                </x-modal>

                <x-modal :name="'delete-lesson-'.$lesson['id']" width="md"
                         :title="'Delete '.$lesson['title'].'?'"
                         subtitle="Lesson">
                    <p>{{ $lesson['title'] }} will leave this module.</p>
                    <x-slot:actions>
                        <x-button variant="ghost" x-on:click="open = false">Cancel</x-button>
                        <form method="POST" action="{{ route('workspace.lessons.destroy', ['platform' => $platformSlug, 'lesson' => $lesson['id']]) }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="course" value="{{ $page['course']['slug'] }}">
                            <x-button type="submit" variant="danger">Delete lesson</x-button>
                        </form>
                    </x-slot:actions>
                </x-modal>
            @endforeach
        @endforeach
    @endif
</x-layouts.platform-workspace>
