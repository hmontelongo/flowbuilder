@props([
    'breadcrumbs' => [
        ['label' => 'Flujos', 'href' => '#'],
        ['label' => 'Nuevo Flujo'],
    ],
])

<header class="flow-builder-topnav flex items-center justify-between pl-5 pr-4 bg-white">
    {{-- Left: Logo + Breadcrumb --}}
    <div class="flex items-center gap-5">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="shrink-0 flex items-center h-6">
            <x-flux::icon.sellia-logo class="h-5 w-auto" />
        </a>

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1">
            @foreach($breadcrumbs as $index => $crumb)
                @if($index > 0)
                    <div class="size-2 flex items-center justify-center">
                        <svg class="size-[3.93px] text-[#4a5565]" viewBox="0 0 4 7" fill="currentColor">
                            <path d="M0 0.5L3.5 3.5L0 6.5V0.5Z"/>
                        </svg>
                    </div>
                @endif
                @if(isset($crumb['href']))
                    <a
                        href="{{ $crumb['href'] }}"
                        class="font-normal text-xs leading-4 text-[#4a5565] underline decoration-solid hover:text-[#1e2939]"
                    >
                        {{ $crumb['label'] }}
                    </a>
                @else
                    <span class="font-normal text-xs leading-4 text-[#4a5565]">
                        {{ $crumb['label'] }}
                    </span>
                @endif
            @endforeach
        </nav>
    </div>

    {{-- Right: Actions --}}
    <div class="flex items-center gap-2">
        <x-flow-builder.sidebar-button icon="help" tooltip="Ayuda" />
        <x-flow-builder.sidebar-button icon="bell" tooltip="Notificaciones" />
    </div>
</header>
