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
import { ref, watch } from 'vue';
import { useNode } from '@vue-flow/core';
import BaseNode from './BaseNode.vue';
import { NodeInput } from './shared';
import { TransferIcon } from './icons';

const { id } = useNode();

const chatCenterValue = ref('');

// Emit DOM events for Alpine/Livewire bridge
const emitDomEvent = (name, detail) => {
    const el = document.getElementById('flow-canvas');
    if (el) {
        el.dispatchEvent(new CustomEvent(name, { detail }));
    }
};

// Watch for changes and emit to parent
watch(chatCenterValue, () => {
    emitDomEvent('node-data-updated', {
        nodeId: id,
        data: {
            chatCenter: chatCenterValue.value,
        },
    });
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
