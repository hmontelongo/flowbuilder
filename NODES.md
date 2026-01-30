# Flow Builder Node Architecture

## Overview

The flow builder uses a **configuration-driven node architecture** built on Vue Flow. All nodes extend `BaseNode.vue` which provides common structure (header, card, connectors) while individual nodes supply their content and configuration.

## File Structure

```
resources/js/components/flow-builder/
├── FlowCanvas.vue          # Canvas container, node registry, Vue Flow setup
├── BaseNode.vue            # Common node structure (header + card + connectors)
├── [NodeType]Node.vue      # Individual node implementations
│   ├── TriggerNode.vue     # Trigger conditions (keyword, regex, etc.)
│   ├── CanalNode.vue       # Channel selection (entry point, no input connector)
│   ├── MessageNode.vue     # Message composition (complex, dynamic connectors)
│   ├── WaitNode.vue        # Wait for response with timeout
│   ├── TagNode.vue         # Tag management
│   ├── TransferNode.vue    # Agent transfer
│   └── PlaceholderNode.vue # Coming soon placeholder
├── MessageContent.vue      # Message type renderer (used by MessageNode)
├── icons/                  # SVG icon components
│   └── index.js            # Barrel export
└── shared/                 # Reusable building blocks
    ├── index.js            # Barrel export
    ├── NodeHeader.vue      # Header with icon chip, label, actions
    ├── NodeCard.vue        # Card container with connector slots
    ├── NodeConnector.vue   # Handle wrapper (source/target)
    ├── PositionedConnector.vue # Absolutely positioned connector
    ├── IconChip.vue        # Colored icon container with error indicator
    ├── NodeTooltip.vue     # Hoverable tooltip
    ├── NodeInput.vue       # Text input
    ├── NodeDropdown.vue    # Dropdown select
    ├── NodeToggle.vue      # Toggle switch
    ├── ChannelDropdown.vue # Channel selector with search
    └── ...                 # Other shared components
```

## The Node Contract

Every node component must:

1. **Import and use `BaseNode`** as the root element
2. **Provide a `nodeConfig` object** with required shape
3. **Use the `#icon` slot** to supply the header icon

### Minimal Node Example

```vue
<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <MyIcon />
        </template>

        <!-- Node content goes here -->
        <div>Your content</div>
    </BaseNode>
</template>

<script setup>
import BaseNode from './BaseNode.vue';
import { MyIcon } from './icons';

const nodeConfig = {
    header: {
        iconColor: 'var(--color-fb-node-mytype)',
        icon: MyIcon,
        tooltip: {
            title: 'My Node',
            description: 'What this node does.',
            linkText: 'Learn more',
            linkUrl: '#',
        },
    },
    connectors: {
        input: true,   // Show left connector
        output: true,  // Show right connector
    },
};
</script>
```

## Config Object Shape

```typescript
interface NodeConfig {
    header: {
        iconColor: string;      // CSS color/variable for icon chip
        icon: Component;        // Vue component for tooltip trigger
        tooltip?: {
            title: string;
            description: string;
            linkText?: string;
            linkUrl?: string;
        };
    };
    connectors: {
        input?: boolean;                    // Show target handle on left
        output?: boolean | OutputConfig;    // Show source handle on right
    };
    card?: {
        width?: string;         // Default: '300px'
        padding?: string;       // Default: '8px'
        borderRadius?: string;  // Default: '8px'
    };
}

interface OutputConfig {
    id?: string;               // Handle ID (for multiple outputs)
    variant?: 'normal' | 'error';  // Visual style
}
```

## When to Use `computed` vs Plain Object

- **Plain object**: When config is static (most nodes)
- **`computed`**: When config depends on reactive state

```vue
// Static config - use plain object
const nodeConfig = { ... };

// Dynamic config - use computed
const nodeConfig = computed(() => ({
    header: { ... },
    connectors: {
        input: true,
        output: !hasButtonOutputs.value,  // Reactive dependency
    },
}));
```

## Advanced Patterns

### Multiple Output Connectors

Use `#extra-connectors` slot with `PositionedConnector`:

```vue
<BaseNode :config="nodeConfig">
    <template #extra-connectors="{ isHandleConnected }">
        <PositionedConnector
            v-for="btn in buttons"
            :key="btn.id"
            :id="`button-${btn.id}`"
            type="source"
            side="right"
            :connected="isHandleConnected(`button-${btn.id}`).value"
            :top="getButtonTop(btn.id)"
        />
    </template>
    ...
</BaseNode>
```

> **Note**: `isHandleConnected()` returns a computed ref for reactivity.
> Always access `.value` when using it.

### DOM-Based Connector Positioning

For connectors that align to dynamic content:

1. Use `ref` on target elements
2. Walk `offsetParent` chain to calculate position relative to card
3. Use `ResizeObserver` to recalculate on layout changes
4. Call recalculation after any content change with `nextTick()`

See `MessageNode.vue` and `WaitNode.vue` for examples.

### Node Actions

`BaseNode` injects `nodeActions` from `FlowCanvas` for delete/duplicate:

```javascript
const nodeActions = inject('nodeActions', null);
const onDelete = () => nodeActions?.deleteNode?.(id);
const onDuplicate = () => nodeActions?.duplicateNode?.(id);
```

### Emitting to Backend

Nodes emit DOM events that Alpine catches and forwards to Livewire:

```javascript
const emitDomEvent = (type, detail) => {
    const canvas = document.getElementById('flow-canvas');
    canvas?.dispatchEvent(new CustomEvent(type, { detail, bubbles: true }));
};

// Example: when node data changes
emitDomEvent('node-data-updated', {
    nodeId: id,
    data: { keyword, exactMatch }
});
```

Available events: `node-data-updated`, `node-added`, `node-deleted`, `node-duplicated`

## Creating a New Node

1. Create `[Name]Node.vue` in the flow-builder directory
2. Import `BaseNode` and your icon
3. Define `nodeConfig` with header and connectors
4. Add your content inside the BaseNode slot
5. Register the node type in `FlowCanvas.vue`:

```javascript
// In FlowCanvas.vue
const nodeTypes = markRaw({
    trigger: TriggerNode,
    canal: CanalNode,
    // Add your new type
    mynode: MyNodeNode,
});
```

## Reference Implementations

- **Simple node**: `TriggerNode.vue` — Static config, form inputs, local state
- **No input**: `CanalNode.vue` — Entry point node pattern
- **Dynamic connectors**: `MessageNode.vue` — Multiple outputs, DOM positioning
- **Dual outputs**: `WaitNode.vue` — Fixed multiple outputs (success/timeout)

## Design Tokens

All nodes use CSS custom properties for consistent theming:

- `--color-fb-node-[type]`: Node type accent colors
- `--color-fb-neutral-[100-900]`: Grayscale palette
- `--color-fb-text`, `--color-fb-icon`: Text and icon colors
- `--color-fb-node-border-error/warning`: Error indicator colors

See `resources/css/app.css` for the full token list.
