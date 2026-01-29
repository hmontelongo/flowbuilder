<template>
    <div class="h-full w-full">
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
            :delete-key-code="['Backspace', 'Delete']"
            :pan-on-drag="isSpacePressed"
            :selection-key-code="selectionEnabled ? true : null"
            :edges-selectable="true"
            :nodes-selectable="true"
            :multi-selection-key-code="['Shift']"
            fit-view-on-init
            :fit-view-options="{ padding: 0.8, maxZoom: 0.5 }"
            @node-drag-stop="onNodeDragStop"
            @connect="onConnect"
            @edges-change="onEdgesChange"
        >
            <Background :gap="12" :size="1" pattern-color="#d1d5db" />
        </VueFlow>
    </div>
</template>

<script setup>
import { ref, markRaw, onMounted, onUnmounted, watch, computed, provide } from 'vue';
import { VueFlow, useVueFlow } from '@vue-flow/core';
import { Background } from '@vue-flow/background';
import CanalNode from './CanalNode.vue';
import TriggerNode from './TriggerNode.vue';
import MessageNode from './MessageNode.vue';
import TransferNode from './TransferNode.vue';
import WaitNode from './WaitNode.vue';
import TagNode from './TagNode.vue';

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

// Vue Flow composable for zoom control
const { fitView, viewport } = useVueFlow();

// Track spacebar state for pan/selection toggle
const isSpacePressed = ref(false);
const selectionEnabled = ref(true);

const handleKeyDown = (e) => {
    if (e.code === 'Space' && !isSpacePressed.value) {
        isSpacePressed.value = true;
        selectionEnabled.value = false; // Disable selection while panning
    }
};

const handleKeyUp = (e) => {
    if (e.code === 'Space') {
        isSpacePressed.value = false;
        selectionEnabled.value = true; // Re-enable selection
    }
};

// Emit DOM events for Alpine/Livewire bridge
const emitDomEvent = (name, detail) => {
    const el = document.getElementById('flow-canvas');
    if (el) {
        el.dispatchEvent(new CustomEvent(name, { detail }));
    }
};

// Listen for fit-view events from Alpine
const handleFitView = () => {
    fitView({ padding: 0.2 });
};

// Add a new node to the canvas
const addNode = (type) => {
    // Calculate position - center of visible viewport with some offset based on existing nodes
    const existingCount = nodes.value.length;
    const baseX = 200 + (existingCount % 5) * 350;
    const baseY = 100 + Math.floor(existingCount / 5) * 200;

    // Map type to display name
    const typeNames = {
        trigger: 'Trigger',
        message: 'Message',
        menu: 'Menu',
        transfer: 'Transfer',
        api: 'API',
        ai: 'AI',
        flag: 'Flag',
        wait: 'Wait',
        knowledge: 'Knowledge',
        condition: 'Condition',
        timer: 'Timer',
        repeat: 'Repeat',
        reset: 'Reset',
        variable: 'Variable',
        tag: 'Tag',
        events: 'Events',
        data: 'Data',
        canal: 'Canal',
    };

    const newNode = {
        id: `node-${crypto.randomUUID()}`,
        type: type,
        position: { x: baseX, y: baseY },
        data: {
            label: typeNames[type] || type,
            state: { mode: 'normal' },
        },
    };

    nodes.value = [...nodes.value, newNode];

    // Emit event for Livewire sync
    emitDomEvent('node-added', {
        nodeId: newNode.id,
        type: newNode.type,
        name: newNode.data.label,
        x: newNode.position.x,
        y: newNode.position.y,
    });
};

// Handle add-node-request from Alpine
const handleAddNodeRequest = (e) => {
    addNode(e.detail.type);
};

onMounted(() => {
    window.addEventListener('fit-view', handleFitView);
    window.addEventListener('keydown', handleKeyDown);
    window.addEventListener('keyup', handleKeyUp);

    // Listen for add-node requests from Alpine/palette
    const flowCanvas = document.getElementById('flow-canvas');
    if (flowCanvas) {
        flowCanvas.addEventListener('add-node-request', handleAddNodeRequest);
    }

    // Emit initial zoom level after mount
    setTimeout(() => {
        window.dispatchEvent(new CustomEvent('zoom-changed', {
            detail: { zoom: Math.round((viewport.value?.zoom || 0.5) * 100) }
        }));
    }, 500);
});

onUnmounted(() => {
    window.removeEventListener('fit-view', handleFitView);
    window.removeEventListener('keydown', handleKeyDown);
    window.removeEventListener('keyup', handleKeyUp);

    const flowCanvas = document.getElementById('flow-canvas');
    if (flowCanvas) {
        flowCanvas.removeEventListener('add-node-request', handleAddNodeRequest);
    }
});

// Watch viewport changes and emit zoom updates
watch(
    () => viewport.value?.zoom,
    (newZoom) => {
        if (newZoom !== undefined) {
            window.dispatchEvent(new CustomEvent('zoom-changed', {
                detail: { zoom: Math.round(newZoom * 100) }
            }));
        }
    }
);

// Node types for custom rendering
const nodeTypes = {
    canal: markRaw(CanalNode),
    trigger: markRaw(TriggerNode),
    message: markRaw(MessageNode),
    transfer: markRaw(TransferNode),
    wait: markRaw(WaitNode),
    tag: markRaw(TagNode),
};

// Default edge styling - bezier curve with selection support
const defaultEdgeOptions = {
    type: 'default',
    style: { stroke: '#d1d5db', strokeWidth: 1.5 },
    animated: false,
    selectable: true,
};

// Transform initial data to Vue Flow format
const transformNodes = (nodeList) => {
    return nodeList.map(node => ({
        id: node.id,
        type: node.type,
        position: { x: node.x, y: node.y },
        data: {
            label: node.name,
            state: node.state || { mode: 'normal' },
        },
    }));
};

const transformEdges = (edgeList) => {
    return edgeList.map(edge => ({
        id: edge.id,
        source: edge.source,
        target: edge.target,
        type: 'default',
        selectable: true,
    }));
};

// Initialize state once from props
const nodes = ref(transformNodes(props.initialNodes));
const edges = ref(transformEdges(props.initialEdges));

// Compute connected node IDs for handle styling
const connectedNodeIds = computed(() => {
    const connected = new Set();
    edges.value.forEach(edge => {
        connected.add(edge.source);
        connected.add(edge.target);
    });
    return connected;
});

// Provide edges to child components for connection detection
provide('flowEdges', edges);
provide('connectedNodeIds', connectedNodeIds);

// Node actions for delete and duplicate
const deleteNode = (nodeId) => {
    // Remove the node
    nodes.value = nodes.value.filter(n => n.id !== nodeId);
    // Remove any edges connected to this node
    const removedEdges = edges.value.filter(e => e.source === nodeId || e.target === nodeId);
    edges.value = edges.value.filter(e => e.source !== nodeId && e.target !== nodeId);
    // Emit events for Livewire sync
    removedEdges.forEach(edge => {
        emitDomEvent('edge-removed', { edgeId: edge.id });
    });
    emitDomEvent('node-deleted', { nodeId });
};

const duplicateNode = (nodeId) => {
    const node = nodes.value.find(n => n.id === nodeId);
    if (node) {
        const newNode = {
            ...node,
            id: `node-${crypto.randomUUID()}`,
            position: {
                x: node.position.x + 50,
                y: node.position.y + 50,
            },
        };
        nodes.value = [...nodes.value, newNode];
        emitDomEvent('node-duplicated', {
            nodeId: newNode.id,
            type: node.type,
            name: node.data?.label || node.type,
            x: newNode.position.x,
            y: newNode.position.y,
        });
    }
};

// Provide node actions to child components
provide('nodeActions', { deleteNode, duplicateNode });

// Event handlers
const onNodeDragStop = (event) => {
    const node = event.node;
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
        id: `edge-${crypto.randomUUID()}`,
        source: connection.source,
        target: connection.target,
        type: 'default',
        selectable: true,
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

.vue-flow {
    width: 100%;
    height: 100%;
    background-color: #fafafa;
    font-family: 'Inter', sans-serif;
}

/* Selection box styling (Figma-like) */
.vue-flow .vue-flow__selection {
    background: rgba(56, 102, 255, 0.08) !important;
    border: 1px solid #3866ff !important;
    border-radius: 2px;
}

/* Selected node styling */
.vue-flow .vue-flow__node.selected {
    outline: none !important;
    box-shadow: none !important;
}

.vue-flow .vue-flow__node.selected > div {
    outline: 2px solid #3866ff;
    outline-offset: 2px;
    border-radius: 8px;
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

/* Selected edge styling */
.vue-flow .vue-flow__edge.selected .vue-flow__edge-path {
    stroke: #3866ff !important;
    stroke-width: 2px !important;
}

/* Edge hover effect */
.vue-flow .vue-flow__edge:hover .vue-flow__edge-path {
    stroke: #6b7280;
    cursor: pointer;
}
</style>
