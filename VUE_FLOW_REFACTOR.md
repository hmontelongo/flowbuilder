# Vue Flow Architecture Refactor - Complete Implementation Plan

**IMPORTANT:** Read this entire document before writing any code. This is a multi-phase refactor that touches many files. Understand the full scope first.

**COMMIT RULE:** Never commit without explicit user approval. Ask before each commit.

---

## The Core Problem

We reimplemented Vue Flow's built-in functionality. The result is fragile code where every change breaks something. We have:

- Manual state arrays (nodes.value, edges.value) that duplicate Vue Flow's internal state
- DOM CustomEvents emitted from every node component
- 7 separate Livewire methods for different actions
- Alpine event listeners catching events and forwarding to Livewire
- Custom keyboard handling that fights Vue Flow's native behavior

Vue Flow provides ALL of this out of the box via useVueFlow(). We should use it.

---

## What Vue Flow Provides (We Reimplemented These)

From useVueFlow() composable:

| Category | Methods/Properties |
|----------|-------------------|
| State | `nodes`, `edges`, `getNodes`, `getEdges` |
| Actions | `addNodes`, `removeNodes`, `addEdges`, `removeEdges`, `updateNode`, `updateNodeData`, `findNode`, `setNodes`, `setEdges`, `toObject`, `fromObject` |
| Events | `onNodesChange`, `onEdgesChange`, `onConnect`, `onNodeDragStart`, `onNodeDrag`, `onNodeDragStop`, `onNodeClick`, `onEdgeClick` |
| Utilities | `screenToFlowCoordinate`, `applyNodeChanges`, `applyEdgeChanges` |

Plus props: `auto-connect`, `connection-radius`, delete handling via change events

---

## Scope of This Refactor

| Phase | Focus | Commit Separately |
|-------|-------|-------------------|
| PHASE 1 | FlowCanvas.vue - Fix State Management | Yes |
| PHASE 2 | All Node Components - Remove DOM Events | Yes |
| PHASE 3 | Livewire Backend - Simplify to Single Sync | Yes |
| PHASE 4 | Blade/Alpine Bridge - Remove Event Listeners | Yes |
| PHASE 5 | Modifiers - Proper Line Behavior | Yes |

Test after each phase before proceeding.

---

## PHASE 1: FlowCanvas.vue

**File:** `resources/js/components/flow-builder/FlowCanvas.vue`

### 1.1 Switch to Controlled Mode

Current template mixes v-model with manual state management. Pick controlled mode.

**Change from:**
```vue
v-model:nodes="nodes"
v-model:edges="edges"
```

**To:**
```vue
:nodes="nodes"
:edges="edges"
:apply-default="false"
```

This means YOU control when changes apply via `applyNodeChanges()` and `applyEdgeChanges()`.

### 1.2 Use auto-connect

**Add to VueFlow component:**
```vue
:auto-connect="edgeConnector"
:connection-radius="30"
```

**Create edgeConnector function:**
```javascript
const edgeConnector = (params) => {
    const newEdge = {
        id: `edge-${crypto.randomUUID()}`,
        source: params.source,
        target: params.target,
        sourceHandle: params.sourceHandle,
        targetHandle: params.targetHandle,
        type: 'default',
        selectable: true,
    };
    return newEdge;
};
```

**Remove:** The manual `onConnect` handler (lines 730-749 in current code).

### 1.3 Remove Native Delete Override

**Remove:** `:delete-key-code="null"` (line 15)

**Remove:** The custom keyboard handler for delete (lines 283-302)

Let Vue Flow handle delete natively - intercept in `onNodesChange`.

### 1.4 Single Change Handlers

All node changes (add, remove, position, selection) go through `onNodesChange`.
All edge changes go through `onEdgesChange`.

**In these handlers:**
1. Process any custom logic (like modifier reconnection)
2. Sync to Livewire (debounced)
3. Call `applyNodeChanges()` or `applyEdgeChanges()`

**onNodesChange implementation:**
```javascript
const onNodesChange = (changes) => {
    for (const change of changes) {
        if (change.type === 'remove') {
            const nodeId = change.id;

            // Handle modifier reconnection
            if (isModifierNode(nodeId)) {
                const incomingEdge = edges.value.find(e => e.target === nodeId);
                const outgoingEdge = edges.value.find(e => e.source === nodeId);

                if (incomingEdge && outgoingEdge) {
                    const reconnectEdge = {
                        id: `edge-${crypto.randomUUID()}`,
                        source: incomingEdge.source,
                        target: outgoingEdge.target,
                        type: 'default',
                        selectable: true,
                    };

                    // Update edges ref
                    edges.value = edges.value
                        .filter(e => e.id !== incomingEdge.id && e.id !== outgoingEdge.id)
                        .concat(reconnectEdge);
                }
            }
        }
    }

    applyNodeChanges(changes);
    syncToLivewire();
};

const onEdgesChange = (changes) => {
    applyEdgeChanges(changes);
    syncToLivewire();
};
```

### 1.5 Simplify Provided Actions

The nodeActions you provide to child nodes should use Vue Flow methods:

```javascript
const deleteNode = (nodeId) => {
    // Create remove change and process through onNodesChange
    onNodesChange([{ type: 'remove', id: nodeId }]);
};

const duplicateNode = (nodeId) => {
    const node = findNode(nodeId);
    if (node) {
        const newNode = {
            ...node,
            id: `node-${crypto.randomUUID()}`,
            position: {
                x: node.position.x + 320,
                y: node.position.y,
            },
            data: { ...node.data },
        };
        addNodes([newNode]);
        syncToLivewire();
    }
};
```

### 1.6 Add Livewire Sync

Instead of emitting separate events for each action, sync the full state:

```javascript
import { useDebounceFn } from '@vueuse/core';

const syncToLivewire = useDebounceFn(() => {
    const state = toObject();
    emitDomEvent('sync-flow', {
        nodes: state.nodes,
        edges: state.edges
    });
}, 300);
```

Call `syncToLivewire()` at the end of `onNodesChange` and `onEdgesChange`.

**Note:** You may need to install @vueuse/core: `npm install @vueuse/core`

### 1.7 Remove Redundant Code

**Delete:**
- Manual `addEdges`/`removeEdges` calls outside of change handlers
- Duplicate modifier reconnection logic (should only be in `onNodesChange`)
- Old event emitting functions (replaced by `sync-flow`)
- Custom keyboard delete handler
- The `@connect` event handler (replaced by `auto-connect`)

**Keep:**
- Node type registration (`nodeTypes`)
- Drag handlers (for collision detection and snap-to-edge)
- `isValidConnection` logic
- Viewport settings
- The template structure
- Space key toggle for pan/selection

### 1.8 Updated useVueFlow Destructuring

```javascript
const {
    fitView,
    viewport,
    getIntersectingNodes,
    updateNode,
    findNode,
    findEdge,
    addNodes,
    applyNodeChanges,
    applyEdgeChanges,
    toObject,
} = useVueFlow();
```

---

## PHASE 2: Node Components

Every node currently emits DOM CustomEvents when data changes. This is wrong.

### Files to Update

| File | Path |
|------|------|
| TriggerNode.vue | `resources/js/components/flow-builder/TriggerNode.vue` |
| CanalNode.vue | `resources/js/components/flow-builder/CanalNode.vue` |
| MessageNode.vue | `resources/js/components/flow-builder/MessageNode.vue` |
| MessageContent.vue | `resources/js/components/flow-builder/MessageContent.vue` |
| WaitNode.vue | `resources/js/components/flow-builder/WaitNode.vue` |
| TagNode.vue | `resources/js/components/flow-builder/TagNode.vue` |
| TransferNode.vue | `resources/js/components/flow-builder/TransferNode.vue` |
| TagModifier.vue | `resources/js/components/flow-builder/TagModifier.vue` |
| TimerModifier.vue | `resources/js/components/flow-builder/TimerModifier.vue` |

### Pattern to Remove (in each file)

Find and remove:
- `emitDomEvent()` function definition
- Calls to `emitDomEvent('node-data-updated', ...)`
- Watchers that emit events

### Pattern to Add

**Use useVueFlow directly:**

```javascript
import { useVueFlow } from '@vue-flow/core';

const { updateNodeData } = useVueFlow();

watch([localKeywords, localAction], () => {
    updateNodeData(props.id, {
        keywords: localKeywords.value,
        action: localAction.value,
        // ... other data fields
    });
}, { deep: true });
```

Each node updates its own data via `updateNodeData()`. The change will be detected by Vue Flow and trigger `onNodesChange` in FlowCanvas, which syncs to Livewire.

### MessageNode Special Case

MessageNode is complex with nested MessageContent.

**Current pattern:** MessageContent emits to MessageNode which then emits to FlowCanvas.

**New pattern:**
1. MessageContent updates its local state
2. MessageContent emits `'update:message'` to MessageNode (keep this, it's standard Vue pattern)
3. MessageNode receives update, calls `updateNodeData()` directly
4. No DOM events needed

---

## PHASE 3: Livewire Backend

**File:** `app/Livewire/FlowBuilder/Canvas.php`

### Current State (Complex)

Canvas.php has separate methods:
- `addNode()`
- `removeNode()`
- `updateNodePosition()`
- `updateNodeData()`
- `addEdge()`
- `removeEdge()`

Each called separately via Alpine event listeners.

### New State (Simple)

**Single method:**

```php
public function syncFlow(array $nodes, array $edges): void
{
    $this->nodes = collect($nodes)->map(function ($node) {
        return [
            'id' => $node['id'],
            'type' => $node['type'],
            'name' => $node['data']['label'] ?? $node['type'],
            'x' => (int) round($node['position']['x']),
            'y' => (int) round($node['position']['y']),
            'data' => $node['data'] ?? [],
            'state' => $node['data']['state'] ?? ['mode' => 'normal'],
        ];
    })->toArray();

    $this->edges = collect($edges)->map(function ($edge) {
        return [
            'id' => $edge['id'],
            'source' => $edge['source'],
            'target' => $edge['target'],
        ];
    })->toArray();

    $this->persist();
}
```

That's it. Full state sync. Livewire doesn't need to know about individual actions.

### Keep

- `mount()` method that loads initial state
- `persist()` method that saves to database
- The `$flow` property binding

### Remove

All the individual action methods (`addNode`, `removeNode`, `updateNodePosition`, `updateNodeData`, `addEdge`, `removeEdge`)

---

## PHASE 4: Blade/Alpine Bridge

**File:** `resources/views/livewire/flow-builder/canvas.blade.php`

### Current State (Complex)

canvas.blade.php has Alpine x-data with:
- Multiple `addEventListener` calls for each event type
- Each listener calls a different `$wire` method

### New State (Simple)

```blade
<div
    id="flow-canvas"
    x-data="{
        init() {
            this.$el.addEventListener('sync-flow', (e) => {
                $wire.syncFlow(e.detail.nodes, e.detail.edges)
            })
        }
    }"
    wire:ignore
>
    <!-- Vue app mounts here -->
</div>
```

Single event listener. Single Livewire call.

### Toolbar

The toolbar buttons should dispatch events that FlowCanvas handles.

**Current:** toolbar emits `'add-node-request'` event

**Keep this for now.** FlowCanvas handles it by calling `addNodes()` from useVueFlow, then `syncToLivewire()`.

---

## PHASE 5: Modifiers

Back to **FlowCanvas.vue** for modifier-specific behavior.

### Snap to Edge on Drag Stop

In `onNodeDragStop`:

```javascript
const onNodeDragStop = (event) => {
    const node = event.node;

    // Clear edge highlight
    clearEdgeHighlight();

    // For unconnected modifiers, check snap-to-edge
    if (node.type?.includes('Modifier')) {
        const hasIncoming = edges.value.some(e => e.target === node.id);
        const hasOutgoing = edges.value.some(e => e.source === node.id);

        if (!hasIncoming && !hasOutgoing) {
            const nearbyEdge = getEdgeNearPoint(node.position, edges.value, nodes.value);
            if (nearbyEdge) {
                splitEdgeWithModifier(nearbyEdge, node.id);
                syncToLivewire();
                return;
            }
        }
    }

    // ... rest of collision detection logic
    syncToLivewire();
};
```

### Reconnect on Delete

Already handled in Phase 1's `onNodesChange` implementation. The modifier reconnection logic should ONLY be in `onNodesChange`, not duplicated elsewhere.

---

## Files Summary

| Phase | File | Action |
|-------|------|--------|
| 1 | `resources/js/components/flow-builder/FlowCanvas.vue` | Refactor state management |
| 2 | `resources/js/components/flow-builder/TriggerNode.vue` | Remove DOM events, use updateNodeData |
| 2 | `resources/js/components/flow-builder/CanalNode.vue` | Remove DOM events, use updateNodeData |
| 2 | `resources/js/components/flow-builder/MessageNode.vue` | Remove DOM events, use updateNodeData |
| 2 | `resources/js/components/flow-builder/MessageContent.vue` | Remove DOM events |
| 2 | `resources/js/components/flow-builder/WaitNode.vue` | Remove DOM events, use updateNodeData |
| 2 | `resources/js/components/flow-builder/TagNode.vue` | Remove DOM events, use updateNodeData |
| 2 | `resources/js/components/flow-builder/TransferNode.vue` | Remove DOM events, use updateNodeData |
| 2 | `resources/js/components/flow-builder/TagModifier.vue` | Remove DOM events, use updateNodeData |
| 2 | `resources/js/components/flow-builder/TimerModifier.vue` | Remove DOM events, use updateNodeData |
| 3 | `app/Livewire/FlowBuilder/Canvas.php` | Simplify to syncFlow |
| 4 | `resources/views/livewire/flow-builder/canvas.blade.php` | Single listener |
| 5 | `resources/js/components/flow-builder/FlowCanvas.vue` | Modifier edge behavior |

---

## What NOT to Change

- `BaseNode.vue` - the config pattern is good
- `BaseModifier.vue` - same
- All `shared/` components - they're fine
- CSS and design tokens - keep all styling
- `NODES.md` documentation - keep it
- The visual design of any node

---

## Testing Checklist

### After PHASE 1:
- [ ] Add node from toolbar - appears on canvas
- [ ] Drag node - position updates
- [ ] Connect two nodes - edge appears
- [ ] Delete node via keyboard - node removed
- [ ] Delete node via button - same behavior
- [ ] Refresh page - state persists

### After PHASE 2:
- [ ] Change data in TriggerNode (add keyword) - persists after refresh
- [ ] Change data in CanalNode (select channel) - persists
- [ ] Change data in MessageNode (add text) - persists
- [ ] Same for all other nodes

### After PHASE 3:
- [ ] All above still works
- [ ] Check network tab - only syncFlow calls, not individual action calls

### After PHASE 4:
- [ ] All above still works
- [ ] Simpler event flow

### After PHASE 5:
- [ ] Place modifier near an edge - it snaps and splits the edge
- [ ] Delete modifier - edge reconnects

---

## Order of Operations

1. Create git branch: `refactor/vue-flow-proper`
2. Commit current state as backup
3. Do Phase 1, **ASK FOR COMMIT APPROVAL**
4. Test Phase 1
5. Do Phase 2, **ASK FOR COMMIT APPROVAL**
6. Test Phase 2
7. Do Phase 3, **ASK FOR COMMIT APPROVAL**
8. Test Phase 3
9. Do Phase 4, **ASK FOR COMMIT APPROVAL**
10. Test Phase 4
11. Do Phase 5, **ASK FOR COMMIT APPROVAL**
12. Test Phase 5
13. Full regression test
14. Merge to main (with user approval)

---

## Current Code Reference

Before starting, read these files to understand current state:
- `FlowCanvas.vue` - main canvas component
- `Canvas.php` - Livewire backend
- `canvas.blade.php` - Blade/Alpine bridge
- Any node component to see the DOM event pattern

**START WITH PHASE 1.** Report what you find in the current code before making changes. Then make changes incrementally.
