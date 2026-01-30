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

                    // Listen for Vue Flow events and forward to Livewire
                    this.$el.addEventListener('node-position-updated', (e) => {
                        $wire.updateNodePosition(e.detail.nodeId, e.detail.x, e.detail.y);
                    });

                    this.$el.addEventListener('edge-added', (e) => {
                        $wire.addEdge(e.detail.edgeId, e.detail.source, e.detail.target);
                    });

                    this.$el.addEventListener('edge-removed', (e) => {
                        $wire.removeEdge(e.detail.edgeId);
                    });

                    this.$el.addEventListener('node-deleted', (e) => {
                        $wire.removeNode(e.detail.nodeId);
                    });

                    this.$el.addEventListener('node-duplicated', (e) => {
                        $wire.addNode(e.detail.nodeId, e.detail.type, e.detail.name, e.detail.x, e.detail.y);
                    });

                    this.$el.addEventListener('node-added', (e) => {
                        $wire.addNode(e.detail.nodeId, e.detail.type, e.detail.name, e.detail.x, e.detail.y);
                    });

                    this.$el.addEventListener('node-data-updated', (e) => {
                        $wire.updateNodeData(e.detail.nodeId, e.detail.data);
                    });
                }
            }"
        ></div>

        {{-- Bottom Toolbar --}}
        <x-flow-builder.bottom-toolbar />

        {{-- Zoom Controls --}}
        <x-flow-builder.zoom-controls />
    </main>
</div>
