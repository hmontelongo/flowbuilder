<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <TransferIcon />
        </template>

        <NodeInput
            v-model="chatCenterValue"
            placeholder="Elige el chat center a transferir"
            icon="search"
        />
    </BaseNode>
</template>

<script setup>
import { ref, watch, inject } from 'vue';
import { useNode, useVueFlow } from '@vue-flow/core';
import BaseNode from './BaseNode.vue';
import { NodeInput } from './shared';
import { TransferIcon } from './icons';

// Vue Flow passes these props to custom nodes
const props = defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
});

// Vue Flow composables
const { id } = useNode();
const { updateNodeData } = useVueFlow();

// Inject syncToLivewire for persisting data changes
const syncToLivewire = inject('syncToLivewire', null);

// Initialize from persisted data
const chatCenterValue = ref(props.data?.chatCenter || '');

// Watch for changes and update node data via Vue Flow
watch(chatCenterValue, () => {
    updateNodeData(id, {
        chatCenter: chatCenterValue.value,
    });
    syncToLivewire?.();
});

const nodeConfig = {
    header: {
        iconColor: 'var(--color-fb-node-transfer)',
        icon: TransferIcon,
        tooltip: {
            title: 'Transferir',
            description: 'Transfiere la conversación a un agente humano o a otro chat center para atención personalizada.',
            linkText: 'Leer más',
            linkUrl: '#',
        },
    },
    connectors: {
        input: true,
        output: true,
    },
};
</script>
