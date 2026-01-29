@props([
    'icon' => null,
    'active' => false,
    'tooltip' => null,
])

<button
    {{ $attributes->merge([
        'type' => 'button',
        'title' => $tooltip,
    ])->class([
        'size-8 rounded-lg flex items-center justify-center transition-colors overflow-hidden',
        'bg-[#e5e7eb]' => $active,
        'bg-white hover:bg-[#f3f4f6]' => !$active,
    ]) }}
>
    @if($icon)
        <x-dynamic-component :component="'flux::icon.' . $icon" class="size-4 text-[#364153]" />
    @else
        {{ $slot }}
    @endif
</button>
