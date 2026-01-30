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
            :is-valid-connection="isValidConnection"
            fit-view-on-init
            :fit-view-options="{ padding: 0.8, maxZoom: 0.5 }"
            @node-drag-start="onNodeDragStart"
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
import PlaceholderNode from './PlaceholderNode.vue';

const props = defineProps({
    initialNodes: {
        type: Array,
        required: true,
    },
    initialEdges: {
        type: Array,
        required: true,
    },
    availableChannels: {
        type: Array,
        default: () => [
            // Default mock data for development
            { id: 'ch-1', type: 'whatsapp', name: 'Canal Principal', phone: '+52 123 456 7890' },
            { id: 'ch-2', type: 'whatsapp', name: 'Canal Ventas', phone: '+52 123 456 7891' },
            { id: 'ch-3', type: 'webchat', name: 'Web Chat', phone: null },
        ],
    },
});

// Vue Flow composable - use built-in intersection detection
const { fitView, viewport, getIntersectingNodes, updateNode } = useVueFlow();

// Default node dimensions (matching NodeCard defaults)
const DEFAULT_NODE_WIDTH = 300;
const DEFAULT_NODE_HEIGHT = 160;
const NODE_GAP = 20;

// Get actual node dimensions from DOM, with fallback to defaults
const getNodeDimensions = () => {
    // Try to measure an existing node in the DOM
    const nodeElement = document.querySelector('.vue-flow__node .node-card-wrapper');
    if (nodeElement) {
        const rect = nodeElement.getBoundingClientRect();
        const zoom = viewport.value.zoom || 1;
        return {
            width: rect.width / zoom,
            height: rect.height / zoom,
        };
    }
    return { width: DEFAULT_NODE_WIDTH, height: DEFAULT_NODE_HEIGHT };
};

// Store original position before drag (for snap-back on collision)
const dragStartPosition = ref(null);

// Check collision using Vue Flow's getIntersectingNodes
const hasIntersection = (nodeId) => {
    const intersecting = getIntersectingNodes({ id: nodeId });
    return intersecting.length > 0;
};

// Simple collision check for position-based checks (add/duplicate)
const checkPositionCollision = (position, excludeNodeId = null) => {
    const { width: NODE_WIDTH, height: NODE_HEIGHT } = getNodeDimensions();
    return nodes.value.some(node => {
        if (node.id === excludeNodeId) return false;
        return !(
            position.x + NODE_WIDTH <= node.position.x ||
            node.position.x + NODE_WIDTH <= position.x ||
            position.y + NODE_HEIGHT <= node.position.y ||
            node.position.y + NODE_HEIGHT <= position.y
        );
    });
};

// Find next free position (grid-like, predictable)
const findNextFreePosition = (baseX, baseY) => {
    const { width: NODE_WIDTH, height: NODE_HEIGHT } = getNodeDimensions();
    let position = { x: baseX, y: baseY };

    if (!checkPositionCollision(position)) {
        return position;
    }

    // Try to the right first
    for (let i = 1; i <= 5; i++) {
        position = { x: baseX + i * (NODE_WIDTH + NODE_GAP), y: baseY };
        if (!checkPositionCollision(position)) {
            return position;
        }
    }

    // Then try rows below
    for (let row = 1; row <= 5; row++) {
        for (let col = 0; col <= 5; col++) {
            position = {
                x: baseX + col * (NODE_WIDTH + NODE_GAP),
                y: baseY + row * (NODE_HEIGHT + NODE_GAP),
            };
            if (!checkPositionCollision(position)) {
                return position;
            }
        }
    }

    return { x: baseX + NODE_WIDTH + NODE_GAP, y: baseY };
};

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
    // Base position - start from a fixed point
    const baseX = 200;
    const baseY = 100;

    // Find the next free position in a grid-like pattern
    const position = findNextFreePosition(baseX, baseY);

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
        position: position,
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

const handleCanvasCleared = () => {
    nodes.value = [];
    edges.value = [];
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

    // Listen for canvas-cleared event from Livewire
    window.addEventListener('canvas-cleared', handleCanvasCleared);

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
    window.removeEventListener('canvas-cleared', handleCanvasCleared);

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
    // Placeholder nodes (not yet designed)
    menu: markRaw(PlaceholderNode),
    api: markRaw(PlaceholderNode),
    ai: markRaw(PlaceholderNode),
    flag: markRaw(PlaceholderNode),
    knowledge: markRaw(PlaceholderNode),
    condition: markRaw(PlaceholderNode),
    timer: markRaw(PlaceholderNode),
    repeat: markRaw(PlaceholderNode),
    reset: markRaw(PlaceholderNode),
    variable: markRaw(PlaceholderNode),
    events: markRaw(PlaceholderNode),
    data: markRaw(PlaceholderNode),
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
            ...node.data, // Spread persisted node data (keywords, action, etc.)
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

// Provide available channels for CanalNode
provide('availableChannels', props.availableChannels);

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
        const { width: NODE_WIDTH, height: NODE_HEIGHT } = getNodeDimensions();

        // Try to place directly to the right of the original
        let position = {
            x: node.position.x + NODE_WIDTH + NODE_GAP,
            y: node.position.y,
        };

        // If that position is taken, try below
        if (checkPositionCollision(position)) {
            position = {
                x: node.position.x,
                y: node.position.y + NODE_HEIGHT + NODE_GAP,
            };
        }

        // If still taken, find next available position nearby
        if (checkPositionCollision(position)) {
            position = findNextFreePosition(node.position.x, node.position.y);
        }

        const newNode = {
            ...node,
            id: `node-${crypto.randomUUID()}`,
            position: position,
            data: { ...node.data },
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
const onNodeDragStart = (event) => {
    // Store original position for snap-back
    dragStartPosition.value = {
        id: event.node.id,
        x: event.node.position.x,
        y: event.node.position.y,
    };
};

const onNodeDragStop = (event) => {
    const node = event.node;

    // Check for collision using Vue Flow's built-in intersection detection
    const intersecting = getIntersectingNodes(node);

    if (intersecting.length > 0 && dragStartPosition.value?.id === node.id) {
        // Collision detected - snap back to original position
        updateNode(node.id, {
            position: {
                x: dragStartPosition.value.x,
                y: dragStartPosition.value.y,
            },
        });

        // Emit the original position (no change)
        emitDomEvent('node-position-updated', {
            nodeId: node.id,
            x: Math.round(dragStartPosition.value.x),
            y: Math.round(dragStartPosition.value.y),
        });
    } else {
        // No collision - emit the new position
        emitDomEvent('node-position-updated', {
            nodeId: node.id,
            x: Math.round(node.position.x),
            y: Math.round(node.position.y),
        });
    }

    // Clear stored position
    dragStartPosition.value = null;
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

// Connection validation - determines if a connection can be made
const isValidConnection = (connection) => {
    // Only block self-connections for now
    return connection.source !== connection.target;
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
    background: white;
    border: 1px solid #3866ff;
    border-radius: 4px !important;
    pointer-events: all;
    transition: all 0.15s ease;
}

.vue-flow .vue-flow__handle:hover {
    background: #eff6ff;
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

/* Connection line styling (line being drawn) */
.vue-flow .vue-flow__connection-line {
    stroke: #3866ff;
    stroke-width: 2;
}

/* Handle states during connection - support both old and new class names */
/* Base handle is 8px, so we offset by half the size increase to keep centered */

/* When dragging a connection line (connecting state) - grows to 10px, offset by -1px */
.vue-flow .vue-flow__handle.connecting,
.vue-flow .vue-flow__handle.vue-flow__handle-connecting {
    width: 10px !important;
    height: 10px !important;
    margin: -1px !important;
    background: #fef3c7 !important;
    border-color: #f59e0b !important;
    border-width: 2px !important;
    border-radius: 50% !important;
}

/* Valid connection target - grows to 12px, offset by -2px */
.vue-flow .vue-flow__handle.valid,
.vue-flow .vue-flow__handle.vue-flow__handle-valid {
    width: 12px !important;
    height: 12px !important;
    margin: -2px !important;
    background: #dcfce7 !important;
    border-color: #22c55e !important;
    border-width: 2px !important;
    border-radius: 50% !important;
    animation: pulse-valid 0.8s ease-in-out infinite;
}

/* Invalid connection - grows to 10px, offset by -1px */
.vue-flow .vue-flow__handle.connecting:not(.valid):not(.vue-flow__handle-valid),
.vue-flow .vue-flow__handle.vue-flow__handle-connecting:not(.valid):not(.vue-flow__handle-valid) {
    width: 10px !important;
    height: 10px !important;
    margin: -1px !important;
    background: #fee2e2 !important;
    border-color: #ef4444 !important;
    border-width: 2px !important;
    border-radius: 50% !important;
    animation: pulse-invalid 0.5s ease-in-out infinite;
}

@keyframes pulse-valid {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.6);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
    }
}

@keyframes pulse-invalid {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0);
    }
}
</style>
