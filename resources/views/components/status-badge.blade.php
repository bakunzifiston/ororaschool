@props(['status' => 'draft'])

@php
    /**
     * Seven states, each with its own hue AND its own treatment — unfilled dashed
     * outline, tinted fill, drawn outline, solid fill — so they stay
     * distinguishable in greyscale and to colour-blind users, not by colour alone.
     *
     * Covers the content lifecycle (draft, pending_review, approved, published,
     * archived) and enrolment states (active, completed) in one component, so no
     * page needs to reimplement a badge.
     */
    $map = [
        'draft' => ['label' => 'Draft', 'class' => 'border border-dashed border-st-draft/60 bg-transparent text-st-draft', 'icon' => null, 'dot' => false],
        'pending_review' => ['label' => 'Pending review', 'class' => 'border border-st-pending/30 bg-st-pending-bg text-st-pending', 'icon' => null, 'dot' => true],
        'approved' => ['label' => 'Approved', 'class' => 'border border-st-approved bg-transparent text-st-approved', 'icon' => 'check', 'dot' => false],
        'published' => ['label' => 'Published', 'class' => 'border border-st-published bg-st-published text-white', 'icon' => null, 'dot' => false],
        'archived' => ['label' => 'Archived', 'class' => 'border border-transparent bg-st-archived-bg text-st-archived', 'icon' => 'archive', 'dot' => false],
        'active' => ['label' => 'Active', 'class' => 'border border-st-active bg-transparent text-st-active', 'icon' => null, 'dot' => true],
        'inactive' => ['label' => 'Inactive', 'class' => 'border border-transparent bg-st-archived-bg text-st-archived', 'icon' => 'archive', 'dot' => false],
        'completed' => ['label' => 'Completed', 'class' => 'border border-st-completed bg-st-completed text-white', 'icon' => 'check', 'dot' => false],
        'valid' => ['label' => 'Valid', 'class' => 'border border-st-active bg-transparent text-st-active', 'icon' => 'check', 'dot' => false],
        'revoked' => ['label' => 'Revoked', 'class' => 'border border-transparent bg-st-archived-bg text-st-archived', 'icon' => 'archive', 'dot' => false],
        'scheduled' => ['label' => 'Scheduled', 'class' => 'border border-st-pending/30 bg-st-pending-bg text-st-pending', 'icon' => null, 'dot' => true],
        'live' => ['label' => 'Live', 'class' => 'border border-st-published bg-st-published text-white', 'icon' => null, 'dot' => true],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'border border-transparent bg-st-archived-bg text-st-archived', 'icon' => 'x', 'dot' => false],
        'free' => ['label' => 'Free', 'class' => 'border border-clay-200 bg-papyrus text-fern-600', 'icon' => null, 'dot' => false],
        'paid' => ['label' => 'Paid', 'class' => 'border border-clay-200 bg-papyrus text-fern-600', 'icon' => null, 'dot' => false],
        'eligible' => ['label' => 'Certificate eligible', 'class' => 'border border-clay-200 bg-papyrus text-fern-600', 'icon' => 'award', 'dot' => false],
        'preview' => ['label' => 'Preview available', 'class' => 'border border-accent-200 bg-accent-50 text-accent-700', 'icon' => null, 'dot' => false],
        'not_found' => ['label' => 'Not found', 'class' => 'border border-st-pending/30 bg-st-pending-bg text-st-pending', 'icon' => null, 'dot' => true],
    ];

    // Tolerate either spelling so fixtures and forms can use whichever reads better.
    $key = str_replace('-', '_', (string) $status);
    $style = $map[$key] ?? [
        'label' => ucfirst(str_replace('_', ' ', $key)),
        'class' => 'border border-clay-200 bg-clay-100 text-fern-500',
        'icon' => null,
        'dot' => false,
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 whitespace-nowrap rounded-sm px-2 py-0.5 text-micro font-medium ' . $style['class']]) }}>
    @if ($style['dot'])
        <span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>
    @endif
    @if ($style['icon'])
        <x-icon :name="$style['icon']" class="h-3 w-3" />
    @endif
    {{ $slot->isEmpty() ? $style['label'] : $slot }}
</span>
