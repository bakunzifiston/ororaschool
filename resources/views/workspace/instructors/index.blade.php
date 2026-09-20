<x-layouts.platform-workspace title="Instructors" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.instructors', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'name', 'label' => 'Instructor'],
                                ['key' => 'district', 'label' => 'District'],
                                ['key' => 'courses_count', 'label' => 'Courses', 'align' => 'right'],
                                ['key' => 'courses', 'label' => 'Teaching'],
                            ]"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="44rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('workspace.instructors.show', ['platform' => $platformSlug, 'instructor' => $row['id']]) }}"
                                   class="flex items-center gap-2">
                                    <x-avatar :name="$row['name']" size="sm" />
                                    <span class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['name'] }}</span>
                                </a>
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['district'] }}</td>
                            <td class="figure px-3 py-2.5 text-right text-micro">{{ $row['courses_count'] }}</td>
                            <td class="px-3 py-2.5 text-micro text-fern-500">
                                {{ count($row['courses']) ? implode(' · ', $row['courses']) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
