<template>
    <div class="h-screen w-screen">
        <VueFlow
            v-model:nodes="nodes"
            v-model:edges="edges"
            :node-types="nodeTypes"
            :default-edge-options="defaultEdgeOptions"
            :connect-on-click="false"
            :snap-to-grid="true"
            :snap-grid="[10, 10]"
            :default-viewport="{ x: 100, y: 100, zoom: 0.5 }"
            :min-zoom="0.2"
            :max-zoom="1.5"
            fit-view-on-init
            :fit-view-options="{ padding: 0.8, maxZoom: 0.5 }"
            @node-drag-stop="onNodeDragStop"
            @connect="onConnect"
            @edges-change="onEdgesChange"
        >
            <Background :gap="12" :size="1" pattern-color="#d1d5db" />
            <Controls />
        </VueFlow>
    </div>
</template>

<script setup>
import { ref, markRaw } from 'vue';
import { VueFlow } from '@vue-flow/core';
import { Background } from '@vue-flow/background';
import { Controls } from '@vue-flow/controls';
import CanalNode from './CanalNode.vue';
import TriggerNode from './TriggerNode.vue';

const props = defineProps({
    initialNodes: {
        type: Array,
        required: true,
    },
    initialEdges: {
        type: Array,
        required: true,
    },
});

// Emit DOM events for Alpine/Livewire bridge
const emitDomEvent = (name, detail) => {
    const el = document.getElementById('flow-canvas');
    if (el) {
        el.dispatchEvent(new CustomEvent(name, { detail }));
    }
};

// Node types for custom rendering
const nodeTypes = {
    canal: markRaw(CanalNode),
    trigger: markRaw(TriggerNode),
};

// Default edge styling - bezier curve
const defaultEdgeOptions = {
    type: 'default',
    style: { stroke: '#d1d5db', strokeWidth: 1.5 },
    animated: false,
};

// Transform initial data to Vue Flow format
const transformNodes = (nodeList) => {
    return nodeList.map(node => ({
        id: node.id,
        type: node.type,
        position: { x: node.x, y: node.y },
        data: { label: node.name },
    }));
};

const transformEdges = (edgeList) => {
    return edgeList.map(edge => ({
        id: edge.id,
        source: edge.source,
        target: edge.target,
        type: 'default',
    }));
};

// Initialize state once from props - don't watch for changes
// Vue Flow manages its own state, we just sync back to Livewire on changes
const nodes = ref(transformNodes(props.initialNodes));
const edges = ref(transformEdges(props.initialEdges));

// Event handlers - sync to Livewire without causing re-render
const onNodeDragStop = (event) => {
    const node = event.node;
    // Debounce the Livewire update to prevent race conditions
    setTimeout(() => {
        emitDomEvent('node-position-updated', {
            nodeId: node.id,
            x: Math.round(node.position.x),
            y: Math.round(node.position.y),
        });
    }, 100);
};

const onConnect = (connection) => {
    const newEdge = {
        id: `edge-${Date.now()}`,
        source: connection.source,
        target: connection.target,
        type: 'default',
    };
    edges.value = [...edges.value, newEdge];
    emitDomEvent('edge-added', {
        edgeId: newEdge.id,
        source: connection.source,
        target: connection.target,
    });
};

const onEdgesChange = (changes) => {
    changes.forEach(change => {
        if (change.type === 'remove') {
            emitDomEvent('edge-removed', { edgeId: change.id });
        }
    });
};
</script>

<style>
@import '@vue-flow/core/dist/style.css';
@import '@vue-flow/core/dist/theme-default.css';
@import '@vue-flow/controls/dist/style.css';

.vue-flow {
    width: 100%;
    height: 100%;
    background-color: #fafafa;
    font-family: 'Inter', sans-serif;
}

/* Custom handle styles - rounded square per Figma */
.vue-flow .vue-flow__handle {
    width: 8px !important;
    height: 8px !important;
    background: white !important;
    border: 1px solid #3866ff !important;
    border-radius: 4px !important;
    pointer-events: all;
}

.vue-flow .vue-flow__handle:hover {
    background: #eff6ff !important;
}

/* Edge styling */
.vue-flow .vue-flow__edge-path {
    stroke: #d1d5db;
    stroke-width: 1.5;
}
</style>
