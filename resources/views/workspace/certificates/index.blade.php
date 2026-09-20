<x-layouts.platform-workspace title="Certificates" :platform="$platformSlug">
    <x-page-header :breadcrumb="$page['header']['breadcrumb']"
                   :title="$page['header']['title']"
                   :subtitle="$page['header']['subtitle']">
        <x-slot:actions>
            <x-button variant="ghost" size="sm" :href="route('workspace.certificates', ['platform' => $platformSlug, 'empty' => 1])">Preview empty</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" action="{{ route('workspace.certificates', ['platform' => $platformSlug]) }}" class="mt-6 flex flex-wrap items-end gap-3">
        <div class="w-48">
            <x-select name="status" label="Status" size="sm" :autosubmit="true"
                      :options="$page['filters']['statuses']"
                      :selected="$page['filters']['status']" />
        </div>
    </form>

    <div class="mt-4">
        <x-panel variant="table" :padded="false">
            <x-data-table :columns="[
                                ['key' => 'code', 'label' => 'Code'],
                                ['key' => 'learner', 'label' => 'Learner'],
                                ['key' => 'course', 'label' => 'Course'],
                                ['key' => 'issued_at', 'label' => 'Issued'],
                                ['key' => 'status', 'label' => 'Status'],
                                ['key' => 'actions', 'label' => '', 'align' => 'right'],
                            ]"
                          :pagination="$page['pagination']"
                          :empty-title="$page['emptyTitle']"
                          :empty-message="$page['emptyMessage']"
                          min-width="52rem">
                @if (count($page['rows']))
                    @foreach ($page['rows'] as $row)
                        <tr class="border-b border-clay-100 last:border-b-0 hover:bg-papyrus">
                            <td class="px-3 py-2.5 font-mono text-micro text-basalt-800">{{ $row['code'] }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['learner'] }}</td>
                            <td class="px-3 py-2.5 text-dense">{{ $row['course'] }}</td>
                            <td class="px-3 py-2.5 text-micro text-fern-500">{{ $row['issued'] }}</td>
                            <td class="px-3 py-2.5"><x-status-badge :status="$row['status']" /></td>
                            <td class="px-3 py-2.5 text-right">
                                <span class="inline-flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('certificates.verify', ['code' => $row['code']]) }}"
                                       class="text-micro font-medium text-accent-700 hover:underline">Verify</a>
                                    @if ($row['status'] === 'valid')
                                        <form method="POST" action="{{ route('workspace.certificates.revoke', ['platform' => $platformSlug, 'certificate' => $row['code']]) }}">
                                            @csrf
                                            <button type="submit" class="text-micro font-medium text-danger hover:underline">Revoke</button>
                                        </form>
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </x-data-table>
        </x-panel>
    </div>
</x-layouts.platform-workspace>
