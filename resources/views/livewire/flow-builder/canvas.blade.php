<div
    id="flow-canvas"
    wire:ignore
    data-nodes="{{ json_encode($nodes) }}"
    data-edges="{{ json_encode($edges) }}"
    x-data="{
        init() {
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
        }
    }"
></div>
