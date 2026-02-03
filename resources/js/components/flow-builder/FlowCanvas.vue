<template>
    <div class="h-full w-full">
        <VueFlow
            v-model:nodes="nodes"
            v-model:edges="edges"
            :node-types="nodeTypes"
            :default-edge-options="defaultEdgeOptions"
            :connect-on-click="false"
            :connection-mode="ConnectionMode.Strict"
            :snap-to-grid="true"
            :snap-grid="[10, 10]"
            :default-viewport="{ x: 100, y: 100, zoom: 0.5 }"
            :min-zoom="0.2"
            :max-zoom="1.5"
            :auto-connect="edgeConnector"
            :connection-radius="30"
            :pan-on-drag="isSpacePressed"
            :selection-key-code="selectionEnabled ? true : null"
            :edges-selectable="true"
            :nodes-selectable="true"
            :multi-selection-key-code="['Shift']"
            :is-valid-connection="isValidConnection"
            fit-view-on-init
            :fit-view-options="{ padding: 0.8, maxZoom: 0.5 }"
            @node-drag-start="onNodeDragStart"
            @node-drag="onNodeDrag"
            @node-drag-stop="onNodeDragStop"
        >
            <Background :gap="12" :size="1" pattern-color="#d1d5db" />
        </VueFlow>
    </div>
</template>

<script setup>
import { ref, markRaw, onMounted, onUnmounted, watch, computed, provide, nextTick } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import { VueFlow, useVueFlow, ConnectionMode } from '@vue-flow/core';
import { Background } from '@vue-flow/background';
import CanalNode from './CanalNode.vue';
import TriggerNode from './TriggerNode.vue';
import MessageNode from './MessageNode.vue';
import TransferNode from './TransferNode.vue';
import WaitNode from './WaitNode.vue';
import PlaceholderNode from './PlaceholderNode.vue';
import TagModifier from './TagModifier.vue';
import TimerModifier from './TimerModifier.vue';

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

// Vue Flow composable - v-model handles state synchronization
const {
    fitView,
    viewport,
    getIntersectingNodes,
    updateNode,
    findNode,
    findEdge,
    addNodes,
    addEdges,
    removeEdges,
    removeNodes,
    toObject,
} = useVueFlow();

// Track original connections to prevent duplicates after splitting
const originalConnections = ref(new Set());

// Debug: track error state toggle for visualizing all nodes in error mode
const debugErrorState = ref(false);

// Track highlighted edge for snap-to-edge visual feedback
const highlightedEdgeId = ref(null);

// Flag to allow programmatic modifier edge creation
// Vue Flow calls isValidConnection for ALL edge changes (including v-model updates)
// This flag bypasses the modifier check during splitEdgeWithModifier
let allowModifierEdges = false;

// Default node dimensions (matching NodeCard defaults)
const DEFAULT_NODE_WIDTH = 300;
const DEFAULT_NODE_HEIGHT = 160;
const NODE_GAP = 20;

// Modifier dimensions (small pill shapes)
const MODIFIER_WIDTH = 120;
const MODIFIER_HEIGHT = 32;

// Get node dimensions based on type - modifiers are smaller than regular nodes
const getNodeDimensions = (nodeType = null) => {
    // Modifiers use smaller dimensions
    if (nodeType?.includes('Modifier')) {
        return { width: MODIFIER_WIDTH, height: MODIFIER_HEIGHT };
    }
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

// Simple collision check for position-based checks (add/duplicate)
// nodeType parameter allows checking with correct dimensions for the new node
const checkPositionCollision = (position, excludeNodeId = null, nodeType = null) => {
    const { width: newNodeWidth, height: newNodeHeight } = getNodeDimensions(nodeType);
    return nodes.value.some(node => {
        if (node.id === excludeNodeId) return false;
        // Get dimensions for the existing node based on its type
        const { width: existingWidth, height: existingHeight } = getNodeDimensions(node.type);
        return !(
            position.x + newNodeWidth <= node.position.x ||
            node.position.x + existingWidth <= position.x ||
            position.y + newNodeHeight <= node.position.y ||
            node.position.y + existingHeight <= position.y
        );
    });
};

// Check if a node is a modifier (compact inline node)
const isModifierNode = (nodeId) => {
    const node = nodes.value.find(n => n.id === nodeId);
    return node?.type?.includes('Modifier');
};

// Calculate distance from a point to a line segment
const pointToLineDistance = (point, lineStart, lineEnd) => {
    const A = point.x - lineStart.x;
    const B = point.y - lineStart.y;
    const C = lineEnd.x - lineStart.x;
    const D = lineEnd.y - lineStart.y;

    const dot = A * C + B * D;
    const lenSq = C * C + D * D;
    const param = lenSq !== 0 ? dot / lenSq : -1;

    let xx, yy;
    if (param < 0) {
        xx = lineStart.x; yy = lineStart.y;
    } else if (param > 1) {
        xx = lineEnd.x; yy = lineEnd.y;
    } else {
        xx = lineStart.x + param * C;
        yy = lineStart.y + param * D;
    }

    const dx = point.x - xx;
    const dy = point.y - yy;
    return Math.sqrt(dx * dx + dy * dy);
};

// Find the closest edge to a point (within threshold)
// Returns the edge with minimum distance, not just the first match
const getEdgeNearPoint = (point, edgeList, nodeList, threshold = 150) => {
    let closestEdge = null;
    let closestDistance = Infinity;

    const pointX = point.x;
    const pointY = point.y;

    for (const edge of edgeList) {
        const sourceNode = nodeList.find(n => n.id === edge.source);
        const targetNode = nodeList.find(n => n.id === edge.target);
        if (!sourceNode || !targetNode) {
            continue;
        }

        // Get dimensions based on node type
        const sourceDims = getNodeDimensions(sourceNode.type);
        const targetDims = getNodeDimensions(targetNode.type);

        // Source output handle is on the right side
        const sourceHandle = {
            x: sourceNode.position.x + sourceDims.width,
            y: sourceNode.position.y + sourceDims.height / 2,
        };
        // Target input handle is on the left side
        const targetHandle = {
            x: targetNode.position.x,
            y: targetNode.position.y + targetDims.height / 2,
        };

        const distance = pointToLineDistance({ x: pointX, y: pointY }, sourceHandle, targetHandle);

        if (distance < threshold && distance < closestDistance) {
            closestEdge = edge;
            closestDistance = distance;
        }
    }
    return closestEdge;
};

// Split an edge by inserting a modifier between source and target
const splitEdgeWithModifier = async (edgeRef, modifierId) => {
    const sourceId = String(edgeRef.source);
    const targetId = String(edgeRef.target);
    const edgeId = String(edgeRef.id);

    if (!sourceId || !targetId) return;

    // Track to prevent manual duplicate connections
    originalConnections.value.add(`${sourceId}->${targetId}`);

    // Enable modifier edge creation (Vue Flow calls isValidConnection for v-model updates)
    allowModifierEdges = true;

    // Remove the original edge first
    removeEdges([edgeId]);

    // Add the two new edges using Vue Flow's API
    addEdges([
        {
            id: `e-${sourceId}-${modifierId}`,
            source: sourceId,
            target: modifierId,
            type: 'default',
            selectable: true,
        },
        {
            id: `e-${modifierId}-${targetId}`,
            source: modifierId,
            target: targetId,
            type: 'default',
            selectable: true,
        },
    ]);

    await nextTick();

    // Disable modifier edge creation after Vue Flow has processed the update
    allowModifierEdges = false;

    syncToLivewire();
};

// Find next free position (grid-like, predictable)
// nodeType parameter allows using correct dimensions for the new node
const findNextFreePosition = (baseX, baseY, nodeType = null) => {
    const { width: NODE_WIDTH, height: NODE_HEIGHT } = getNodeDimensions(nodeType);
    let position = { x: baseX, y: baseY };

    if (!checkPositionCollision(position, null, nodeType)) {
        return position;
    }

    // Try to the right first
    for (let i = 1; i <= 5; i++) {
        position = { x: baseX + i * (NODE_WIDTH + NODE_GAP), y: baseY };
        if (!checkPositionCollision(position, null, nodeType)) {
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
            if (!checkPositionCollision(position, null, nodeType)) {
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
    // Delete is now handled natively by Vue Flow - intercepted in onNodesChange
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

// Auto-connect edge connector - creates edges automatically when connections are made
const edgeConnector = (params) => {
    return {
        id: `edge-${crypto.randomUUID()}`,
        source: params.source,
        target: params.target,
        sourceHandle: params.sourceHandle,
        targetHandle: params.targetHandle,
        type: 'default',
        selectable: true,
    };
};

// Debounced sync to Livewire - single state sync instead of individual events
const syncToLivewire = useDebounceFn(() => {
    const state = toObject();
    emitDomEvent('sync-flow', {
        nodes: state.nodes,
        edges: state.edges,
    });
}, 300);

// Listen for fit-view events from Alpine
const handleFitView = () => {
    fitView({ padding: 0.2 });
};

// Calculate position within the current viewport
const getViewportCenterPosition = () => {
    const vp = viewport.value;
    // Get the canvas element to know its dimensions
    const canvasEl = document.getElementById('flow-canvas');
    if (!canvasEl || !vp) {
        return { x: 200, y: 100 }; // Fallback
    }

    const rect = canvasEl.getBoundingClientRect();
    // Convert screen center to flow coordinates
    const centerX = (rect.width / 2 - vp.x) / vp.zoom;
    const centerY = (rect.height / 2 - vp.y) / vp.zoom;

    return { x: Math.round(centerX), y: Math.round(centerY) };
};

// Add a new node to the canvas
const addNode = (type) => {
    // Get position within viewport center
    const viewportCenter = getViewportCenterPosition();

    // Find the next free position starting from viewport center
    // Pass type to use correct dimensions for collision detection
    const position = findNextFreePosition(viewportCenter.x, viewportCenter.y, type);

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
        events: 'Events',
        data: 'Data',
        canal: 'Canal',
        // Modifiers
        tagModifier: 'Tag Modifier',
        timerModifier: 'Timer',
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

    addNodes([newNode]);
    syncToLivewire();
};

// Handle add-node-request from Alpine
const handleAddNodeRequest = (e) => {
    addNode(e.detail.type);
};

const handleCanvasCleared = () => {
    nodes.value = [];
    edges.value = [];
};

// Debug: Toggle error state on all nodes
const handleToggleErrorState = () => {
    debugErrorState.value = !debugErrorState.value;
    const newState = debugErrorState.value
        ? { mode: 'error', errorLevel: 'error' }
        : { mode: 'normal' };

    nodes.value.forEach(node => {
        updateNode(node.id, {
            data: {
                ...node.data,
                state: newState,
            },
        });
    });
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

    // Listen for toggle-error-state event from toolbar
    window.addEventListener('toggle-error-state', handleToggleErrorState);

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
    window.removeEventListener('toggle-error-state', handleToggleErrorState);

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
    // Modifiers - compact inline nodes
    tagModifier: markRaw(TagModifier),
    timerModifier: markRaw(TimerModifier),
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

// Watch for node data changes from child components (via updateNodeData)
// This ensures changes made by child nodes are synced to Livewire
watch(nodes, () => {
    syncToLivewire();
}, { deep: true });

// Watch for edge changes (connections created/deleted)
// This ensures new edges are persisted
watch(edges, () => {
    syncToLivewire();
}, { deep: true });

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
const deleteNode = async (nodeId) => {
    // Handle modifier reconnection before deletion
    if (isModifierNode(nodeId)) {
        await handleModifierDeletion(nodeId);
    }
    // Remove the node using Vue Flow's removeNodes
    removeNodes([nodeId]);
    syncToLivewire();
};

const duplicateNode = (nodeId) => {
    const node = findNode(nodeId);
    if (node) {
        const { width: NODE_WIDTH, height: NODE_HEIGHT } = getNodeDimensions(node.type);

        // Try to place directly to the right of the original
        let position = {
            x: node.position.x + NODE_WIDTH + NODE_GAP,
            y: node.position.y,
        };

        // If that position is taken, try below
        if (checkPositionCollision(position, null, node.type)) {
            position = {
                x: node.position.x,
                y: node.position.y + NODE_HEIGHT + NODE_GAP,
            };
        }

        // If still taken, find next available position nearby
        if (checkPositionCollision(position, null, node.type)) {
            position = findNextFreePosition(node.position.x, node.position.y, node.type);
        }

        const newNode = {
            ...node,
            id: `node-${crypto.randomUUID()}`,
            position: position,
            data: { ...node.data },
        };
        addNodes([newNode]);
        syncToLivewire();
    }
};

// Provide node actions to child components
provide('nodeActions', { deleteNode, duplicateNode });

// Provide syncToLivewire for child components to call after updating node data
provide('syncToLivewire', syncToLivewire);

// Event handlers
const onNodeDragStart = (event) => {
    // Store original position for snap-back
    dragStartPosition.value = {
        id: event.node.id,
        x: event.node.position.x,
        y: event.node.position.y,
    };
    // Clear any previous highlight
    clearEdgeHighlight();
};

// Handle continuous drag for snap-to-edge visual feedback
const onNodeDrag = (event) => {
    const node = event.node;

    // Only show highlight for unconnected modifiers
    if (!node.type?.includes('Modifier')) {
        clearEdgeHighlight();
        return;
    }

    const hasIncoming = edges.value.some(e => e.target === node.id);
    const hasOutgoing = edges.value.some(e => e.source === node.id);

    if (hasIncoming || hasOutgoing) {
        clearEdgeHighlight();
        return;
    }

    // Check if near an edge (threshold 500 accounts for flow coordinate scale at various zoom levels)
    const nearbyEdge = getEdgeNearPoint(node.position, edges.value, nodes.value);

    if (nearbyEdge && nearbyEdge.id !== highlightedEdgeId.value) {
        // Clear previous highlight and set new one
        clearEdgeHighlight();
        highlightedEdgeId.value = nearbyEdge.id;
        // Use findEdge and direct mutation for proper reactivity
        const edge = findEdge(nearbyEdge.id);
        if (edge) {
            edge.style = { stroke: '#3866ff', strokeWidth: 2.5 };
            edge.animated = true;
        }
    } else if (!nearbyEdge && highlightedEdgeId.value) {
        clearEdgeHighlight();
    }
};

// Clear edge highlight
const clearEdgeHighlight = () => {
    if (highlightedEdgeId.value) {
        const edge = findEdge(highlightedEdgeId.value);
        if (edge) {
            edge.style = { stroke: '#d1d5db', strokeWidth: 1.5 };
            edge.animated = false;
        }
        highlightedEdgeId.value = null;
    }
};

const onNodeDragStop = async (event) => {
    const node = event.node;

    // Clear edge highlight
    clearEdgeHighlight();

    // For unconnected modifiers, check snap-to-edge FIRST (before collision detection)
    // This allows modifiers to snap onto edges even when near other nodes
    if (node.type?.includes('Modifier')) {
        const hasIncoming = edges.value.some(e => e.target === node.id);
        const hasOutgoing = edges.value.some(e => e.source === node.id);

        if (!hasIncoming && !hasOutgoing) {
            const nearbyEdge = getEdgeNearPoint(node.position, edges.value, nodes.value);
            if (nearbyEdge) {
                await splitEdgeWithModifier(nearbyEdge, node.id);
                // Clear stored position and return - edge snap takes priority
                dragStartPosition.value = null;
                return;
            }
        }
    }

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
    }

    // Clear stored position and sync state
    dragStartPosition.value = null;
    syncToLivewire();
};

// Handle modifier deletion - reconnect the edges when a modifier is deleted
// Called from deleteNode action
const handleModifierDeletion = async (nodeId) => {
    const incomingEdge = edges.value.find(e => e.target === nodeId);
    const outgoingEdge = edges.value.find(e => e.source === nodeId);

    if (incomingEdge && outgoingEdge) {
        // Clean up originalConnections when modifier is deleted
        const reconnectionKey = `${incomingEdge.source}->${outgoingEdge.target}`;
        originalConnections.value.delete(reconnectionKey);

        // Remove the modifier's edges
        removeEdges([incomingEdge.id, outgoingEdge.id]);

        // Enable modifier edges - target might be another modifier in a chain
        allowModifierEdges = true;

        // Add the reconnection edge
        addEdges([{
            id: `edge-${crypto.randomUUID()}`,
            source: incomingEdge.source,
            target: outgoingEdge.target,
            type: 'default',
            selectable: true,
        }]);

        await nextTick();
        allowModifierEdges = false;
    }
};

// Connection validation - determines if a connection can be made
const isValidConnection = (connection) => {
    // Block self-connections
    if (connection.source === connection.target) {
        return false;
    }

    // Block manual connections to/from modifiers (but allow programmatic ones)
    // Modifiers can only be connected via drag-onto-edge (splitEdgeWithModifier)
    const sourceNode = nodes.value.find(n => n.id === connection.source);
    const targetNode = nodes.value.find(n => n.id === connection.target);
    if (!allowModifierEdges && (sourceNode?.type?.includes('Modifier') || targetNode?.type?.includes('Modifier'))) {
        return false;
    }

    // Block duplicate connections (same source → target already exists)
    const duplicateExists = edges.value.some(
        edge => edge.source === connection.source && edge.target === connection.target
    );
    if (duplicateExists) {
        return false;
    }

    // Block backward connections (would create a cycle)
    // Check if target is already upstream of source (i.e., there's a path from target to source)
    const reverseExists = edges.value.some(
        edge => edge.source === connection.target && edge.target === connection.source
    );
    if (reverseExists) {
        return false;
    }

    // Block cycle-creating connections using BFS
    // If we can reach the source from the target through existing edges, it would create a cycle
    const canReachSourceFromTarget = (targetId, sourceId) => {
        const visited = new Set();
        const queue = [targetId];

        while (queue.length > 0) {
            const currentId = queue.shift();
            if (currentId === sourceId) {
                return true; // Found a path from target to source - would create cycle
            }
            if (visited.has(currentId)) {
                continue;
            }
            visited.add(currentId);

            // Find all nodes that this node connects to (outgoing edges)
            for (const edge of edges.value) {
                if (edge.source === currentId && !visited.has(edge.target)) {
                    queue.push(edge.target);
                }
            }
        }
        return false;
    };

    if (canReachSourceFromTarget(connection.target, connection.source)) {
        return false;
    }

    // Block connections that were split by a modifier (prevents duplicates after tag insertion)
    const connectionKey = `${connection.source}->${connection.target}`;
    if (originalConnections.value.has(connectionKey)) {
        return false;
    }

    return true;
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
    outline: 1.5px solid #3866ff;
    outline-offset: 2px;
    border-radius: 8px;
}

/* Selected blocks - reset border to avoid stacking with outline */
.vue-flow .vue-flow__node.selected .node-card {
    border-color: var(--color-fb-node-border-normal, #e5e7eb) !important;
}

/* Modifiers handle their own selection styling */
.vue-flow .vue-flow__node.selected > div:has(.tag-modifier),
.vue-flow .vue-flow__node.selected > div:has(.timer-modifier) {
    outline: none;
}

/* Selected modifier - outline only, reset border */
.vue-flow .vue-flow__node.selected .tag-modifier,
.vue-flow .vue-flow__node.selected .timer-modifier {
    border-color: var(--color-fb-node-border-normal, #e5e7eb) !important;
    outline: 1.5px solid #3866ff;
    outline-offset: 2px;
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

/* Highlighted edge for snap-to-edge feedback */
.vue-flow .vue-flow__edge.animated .vue-flow__edge-path {
    stroke-dasharray: 5;
    animation: edge-dash 0.5s linear infinite;
}

@keyframes edge-dash {
    to {
        stroke-dashoffset: -10;
    }
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
