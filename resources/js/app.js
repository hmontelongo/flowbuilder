import { createApp } from 'vue';
import FlowCanvas from './components/flow-builder/FlowCanvas.vue';

// Mount Vue Flow canvas when the element is present
document.addEventListener('DOMContentLoaded', () => {
    const canvasEl = document.getElementById('flow-canvas');

    if (canvasEl) {
        // Get initial data from data attributes
        const initialNodes = JSON.parse(canvasEl.dataset.nodes || '[]');
        const initialEdges = JSON.parse(canvasEl.dataset.edges || '[]');

        const app = createApp(FlowCanvas, {
            initialNodes,
            initialEdges,
        });

        // Handle events from Vue and bridge to Livewire
        app.config.globalProperties.$emitToLivewire = (event, data) => {
            if (window.Livewire) {
                const component = Livewire.find(canvasEl.closest('[wire\\:id]')?.getAttribute('wire:id'));
                if (component) {
                    component.call(event, ...Object.values(data));
                }
            }
        };

        const vm = app.mount(canvasEl);

        // Listen for Vue events and forward to Livewire
        canvasEl.__vueApp__ = app;
        canvasEl.__vueInstance__ = vm;
    }
});

// Re-mount on Livewire navigation
document.addEventListener('livewire:navigated', () => {
    const canvasEl = document.getElementById('flow-canvas');

    if (canvasEl && !canvasEl.__vueApp__) {
        const initialNodes = JSON.parse(canvasEl.dataset.nodes || '[]');
        const initialEdges = JSON.parse(canvasEl.dataset.edges || '[]');

        const app = createApp(FlowCanvas, {
            initialNodes,
            initialEdges,
        });

        const vm = app.mount(canvasEl);
        canvasEl.__vueApp__ = app;
        canvasEl.__vueInstance__ = vm;
    }
});
