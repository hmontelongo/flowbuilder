@props([
    'activeTool' => 'flows',
])

@php
$mainTools = [
    ['id' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
    ['id' => 'campaigns', 'icon' => 'bullhorn', 'label' => 'Campañas'],
    ['id' => 'flows', 'icon' => 'edge-device', 'label' => 'Flujos'],
    ['id' => 'tree-view', 'icon' => 'tree-view', 'label' => 'Vista de árbol'],
    ['id' => 'assistant', 'icon' => 'watson-assistant', 'label' => 'Asistente'],
    ['id' => 'whatsapp', 'icon' => 'whatsapp', 'label' => 'WhatsApp'],
    ['id' => 'events', 'icon' => 'events', 'label' => 'Eventos'],
    ['id' => 'chat', 'icon' => 'chat', 'label' => 'Chat'],
    ['id' => 'knowledge', 'icon' => 'share-knowledge', 'label' => 'Conocimiento'],
    ['id' => 'reports', 'icon' => 'report', 'label' => 'Reportes'],
    ['id' => 'qa', 'icon' => 'question-answering', 'label' => 'Preguntas'],
    ['id' => 'tts', 'icon' => 'text-to-speech', 'label' => 'Texto a voz'],
];

$bottomTools = [
    ['id' => 'user', 'icon' => 'user', 'label' => 'Perfil'],
    ['id' => 'settings', 'icon' => 'settings', 'label' => 'Configuración'],
    ['id' => 'panel', 'icon' => 'panel-toggle', 'label' => 'Panel'],
];
@endphp

<aside class="flow-builder-sidebar flex flex-col items-center justify-between pt-1 pb-2 bg-white">
    {{-- Main tools --}}
    <nav class="flex flex-col items-center gap-1">
        @foreach($mainTools as $tool)
            <x-flow-builder.sidebar-button
                :icon="$tool['icon']"
                :tooltip="$tool['label']"
                :active="$activeTool === $tool['id']"
                wire:click="setActiveTool('{{ $tool['id'] }}')"
            />
        @endforeach
    </nav>

    {{-- Bottom tools --}}
    {{-- TODO: Add wire:click handlers for profile, settings, and panel toggle --}}
    <nav class="flex flex-col items-center gap-1">
        @foreach($bottomTools as $tool)
            <x-flow-builder.sidebar-button
                :icon="$tool['icon']"
                :tooltip="$tool['label']"
            />
        @endforeach
    </nav>
</aside>
