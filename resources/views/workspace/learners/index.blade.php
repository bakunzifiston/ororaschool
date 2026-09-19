<x-layouts.platform-workspace title="Learners" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.learners', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mt-6">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'name', 'label' => 'Learner'],
                                ['key' => 'cohort', 'label' => 'Cohort'],
                                ['key' => 'district', 'label' => 'District'],
                                ['key' => 'enrolled', 'label' => 'Enrolments', 'align' => 'right'],
                                ['key' => 'progress', 'label' => 'Progress'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="48rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5">
                                <a href="{{ route('workspace.learners.show', ['platform' => $platformSlug, 'learner' => $row['id']]) }}"
                                   class="flex items-center gap-2">
                                    <x-avatar :name="$row['name']" size="sm" />
                                    <span class="font-medium text-basalt-900 hover:text-accent-600">{{ $row['name'] }}</span>
                                </a>
                            </td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['cohort'] ?? '—' }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['district'] }}</td>
                            <td class="figure px-3 py-2.5 text-right text-micro">{{ $row['enrolled'] ?? 0 }}</td>
                            <td class="px-3 py-2.5"><x-progress-bar :value="(int) ($row['progress'] ?? 0)" size="sm" class="max-w-[9rem]" /></td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
