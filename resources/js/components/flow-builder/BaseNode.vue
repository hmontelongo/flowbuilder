<template>
    <NodeWrapper @delete="onDelete" @duplicate="onDuplicate">
        <div class="base-node flex flex-col items-center gap-2">
            <!-- Header -->
            <NodeHeader :label="node.data?.label ?? 'Node'" :icon-color="config.header.iconColor">
                <template #icon>
                    <slot name="icon">
                        <component
                            v-if="typeof config.header.icon === 'object'"
                            :is="config.header.icon"
                            class="w-4 h-4"
                        />
                    </slot>
                </template>
            </NodeHeader>

            <!-- Card with connectors -->
            <NodeCard
                :state="nodeState"
                :padding="config.card?.padding ?? '16px 8px'"
                :border-radius="config.card?.borderRadius ?? '8px 8px 4px 8px'"
                :width="config.card?.width ?? '300px'"
            >
                <template v-if="hasInputConnector" #input-connector>
                    <NodeConnector
                        type="target"
                        side="left"
                        :connected="isInputConnected"
                    />
                </template>

                <template v-if="hasOutputConnector" #output-connector>
                    <NodeConnector
                        :id="outputConfig?.id"
                        type="source"
                        side="right"
                        :variant="outputConfig?.variant ?? 'normal'"
                        :connected="isOutputConnected"
                    />
                </template>

                <template v-if="$slots['extra-connectors']" #extra-connectors>
                    <slot name="extra-connectors" :is-handle-connected="isHandleConnected" />
                </template>

                <!-- Main content slot -->
                <slot />
            </NodeCard>
        </div>
    </NodeWrapper>
</template>

<script setup>
import { computed, inject } from 'vue';
import { useNode } from '@vue-flow/core';
import { NodeWrapper, NodeCard, NodeHeader, NodeConnector } from './shared';

const props = defineProps({
    config: {
        type: Object,
        required: true,
        validator: (val) => val.header?.iconColor !== undefined && val.connectors !== undefined,
    },
});

// Vue Flow's useNode provides id, node object, and connected edges
const { id, node, connectedEdges } = useNode();

// Compute connection states from Vue Flow's data
const isInputConnected = computed(() =>
    connectedEdges.value.some(edge => edge.target === id)
);

const isOutputConnected = computed(() =>
    connectedEdges.value.some(edge => edge.source === id)
);

// For nodes with multiple output handles (like WaitNode)
const isHandleConnected = (handleId) => computed(() =>
    connectedEdges.value.some(edge =>
        (edge.source === id && edge.sourceHandle === handleId) ||
        (edge.target === id && edge.targetHandle === handleId)
    )
);

// Node state derived from data
const nodeState = computed(() => node.data?.state ?? { mode: 'normal' });

// Connector configuration helpers
const hasInputConnector = computed(() => !!props.config.connectors?.input);
const hasOutputConnector = computed(() => !!props.config.connectors?.output);
const outputConfig = computed(() => {
    const output = props.config.connectors?.output;
    return typeof output === 'object' ? output : {};
});

// Node actions via inject (keeps existing pattern from FlowCanvas)
const nodeActions = inject('nodeActions', null);
const onDelete = () => nodeActions?.deleteNode?.(id);
const onDuplicate = () => nodeActions?.duplicateNode?.(id);
</script>

<style scoped>
.base-node {
    position: relative;
}
</style>
