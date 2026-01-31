<template>
    <div class="base-modifier">
        <slot
            :id="id"
            :is-input-connected="isInputConnected"
            :is-output-connected="isOutputConnected"
            :on-delete="onDelete"
            :emit-data-update="emitDataUpdate"
        />
    </div>
</template>

<script setup>
import { computed, inject } from 'vue';
import { useNode, useVueFlow } from '@vue-flow/core';

// Vue Flow passes these props to custom nodes
defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
});

// Vue Flow composables
const { id, connectedEdges } = useNode();
const { updateNodeData } = useVueFlow();

// Connection states
const isInputConnected = computed(() =>
    connectedEdges.value.some(edge => edge.target === id)
);

const isOutputConnected = computed(() =>
    connectedEdges.value.some(edge => edge.source === id)
);

// Node actions via inject
const nodeActions = inject('nodeActions', null);
const onDelete = () => nodeActions?.deleteNode?.(id);

// Sync to Livewire via inject
const syncToLivewire = inject('syncToLivewire', null);

// Update node data via Vue Flow and trigger sync
const emitDataUpdate = (data) => {
    updateNodeData(id, data);
    syncToLivewire?.();
};
</script>
