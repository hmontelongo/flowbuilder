<div class="flow-builder-layout">
    {{-- Top Navigation --}}
    <x-flow-builder.top-nav />

    {{-- Left Sidebar --}}
    <x-flow-builder.left-sidebar :active-tool="$activeTool" />

    {{-- Main Content Area --}}
    <main class="flow-builder-main">
        {{-- Canvas Header (inside the main content, not the app header) --}}
        <x-flow-builder.canvas-header :title="$flowName" />

        {{-- Block Palette (left side of canvas) --}}
        <x-flow-builder.block-palette />

        {{-- Vue Flow Canvas --}}
        <div
            id="flow-canvas"
            class="absolute inset-0 top-[48px]"
            wire:ignore
            data-nodes="{{ json_encode($nodes) }}"
            data-edges="{{ json_encode($edges) }}"
            x-data="{
                init() {
                    // Listen for add-block events from the palette
                    window.addEventListener('add-block', (e) => {
                        this.$el.dispatchEvent(new CustomEvent('add-node-request', {
                            detail: { type: e.detail.type }
                        }));
                    });

                    // Full state sync from Vue Flow
                    this.$el.addEventListener('sync-flow', (e) => {
                        $wire.syncFlow(e.detail.nodes, e.detail.edges);
                    });
                }
            }"
        ></div>

        {{-- Bottom Toolbar --}}
        <x-flow-builder.bottom-toolbar />

        {{-- Zoom Controls --}}
        <x-flow-builder.zoom-controls />

        {{-- Dev Toolbar --}}
        <x-flow-builder.dev-toolbar />
    </main>
</div>
