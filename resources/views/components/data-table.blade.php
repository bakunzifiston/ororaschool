@props([
    'columns' => [],
    'rows' => [],
    'pagination' => null,
    'emptyTitle' => 'Nothing here yet',
    'emptyMessage' => null,
    'minWidth' => '44rem',
])

@php
    /**
     * Two ways to use this, so no page ever hardcodes a table:
     *   1. Pass :columns and :rows and let it render — column specs carry
     *      `align`, `numeric` and `type` (status | role | person | progress).
     *   2. Pass :columns for the head and supply <tr> rows in the default slot
     *      when a page needs bespoke cells.
     */
    $useSlot = $slot->hasActualContent();
    $hasRows = $useSlot || count($rows) > 0;
@endphp

{{-- min-w-0 on both wrappers: without it the table's min-width propagates up
     through grid items and drags the whole page into horizontal overflow. --}}
<div class="w-full min-w-0">
    @if ($hasRows)
        <div class="w-full min-w-0 overflow-x-auto">
            <table class="w-full border-collapse text-dense" style="min-width: {{ $minWidth }}">
                <thead>
                    {{-- Ledger rule: one heavier line under the head, hairlines between records. --}}
                    <tr class="border-b-2 border-basalt-800">
                        @foreach ($columns as $column)
                            <th scope="col"
                                @class([
                                    'px-3 py-2 text-micro font-medium text-fern-500',
                                    'text-right' => ($column['align'] ?? 'left') === 'right',
                                    'text-left' => ($column['align'] ?? 'left') !== 'right',
                                ])>
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @if ($useSlot)
                        {{ $slot }}
                    @else
                        @foreach ($rows as $row)
                            <tr class="border-b border-clay-100 transition-colors last:border-b-0 hover:bg-papyrus">
                                @foreach ($columns as $column)
                                    @php $value = $row[$column['key']] ?? null; @endphp
                                    <td @class([
                                        'px-3 py-2.5 align-middle',
                                        'text-right' => ($column['align'] ?? 'left') === 'right',
                                        'figure text-micro text-fern-500' => $column['numeric'] ?? false,
                                        'text-basalt-800' => ! ($column['numeric'] ?? false),
                                    ])>
                                        @switch($column['type'] ?? null)
                                            @case('status')
                                                <x-status-badge :status="$value ?? 'draft'" />
                                                @break

                                            @case('role')
                                                <x-role-chip :role="$value ?? 'learner'" />
                                                @break

                                            @case('person')
                                                <span class="flex items-center gap-2">
                                                    <x-avatar :name="$value" size="sm" />
                                                    <span class="truncate">{{ $value }}</span>
                                                </span>
                                                @break

                                            @case('progress')
                                                <x-progress-bar :value="(int) $value" size="sm" class="max-w-[9rem]" />
                                                @break

                                            @default
                                                @if (is_int($value) && ($column['numeric'] ?? false))
                                                    {{ number_format($value) }}
                                                @else
                                                    <span class="line-clamp-2">{{ $value }}</span>
                                                @endif
                                        @endswitch
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        @if ($pagination)
            <x-pagination :pagination="$pagination" class="border-t border-clay-200" />
        @endif
    @else
        <x-empty-state :title="$emptyTitle" :message="$emptyMessage" icon="inbox">
            @isset($emptyActions)
                <x-slot:actions>{{ $emptyActions }}</x-slot:actions>
            @endisset
        </x-empty-state>
    @endif
</div>
