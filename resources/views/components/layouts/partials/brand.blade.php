@props(['href' => '/', 'onBasalt' => false])

<a href="{{ $href }}"
   class="flex items-center gap-2 rounded-sm py-1.5 font-display text-dense font-semibold {{ $onBasalt ? 'text-white' : 'text-basalt-900' }}">
    <x-icon name="sprout" class="h-4 w-4 {{ $onBasalt ? 'text-accent-400' : 'text-accent-500' }}" />
    Orora School
</a>
