{{-- Shared flash-message region. Present in every layout via the shell, so any
     page or future Livewire action can flash without adding markup. --}}
@php
    $messages = array_filter([
        'ok' => session('status') ?? session('success'),
        'warn' => session('warning'),
        'error' => session('error'),
    ]);
@endphp

@if (count($messages))
    <div class="px-4 pt-4 sm:px-6 lg:px-8" role="status" aria-live="polite">
        <div class="mx-auto flex max-w-6xl flex-col gap-2">
            @foreach ($messages as $tone => $message)
                @php
                    $styles = [
                        'ok' => ['class' => 'border-ok/30 bg-ok-bg text-ok', 'icon' => 'check'],
                        'warn' => ['class' => 'border-st-pending/30 bg-st-pending-bg text-st-pending', 'icon' => 'alert'],
                        'error' => ['class' => 'border-danger/30 bg-danger-bg text-danger', 'icon' => 'alert'],
                    ][$tone];
                @endphp

                <div x-data="{ shown: true }" x-show="shown"
                     class="flex items-start gap-2.5 rounded-md border px-3 py-2.5 text-dense {{ $styles['class'] }}">
                    <x-icon :name="$styles['icon']" class="mt-0.5 h-4 w-4" />
                    <p class="min-w-0 grow">{{ $message }}</p>
                    <button type="button" x-on:click="shown = false"
                            class="-mr-0.5 rounded-xs p-0.5 opacity-70 hover:opacity-100"
                            aria-label="Dismiss message">
                        <x-icon name="x" class="h-3.5 w-3.5" />
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endif
