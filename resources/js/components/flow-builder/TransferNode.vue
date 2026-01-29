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
import { ref } from 'vue';
import BaseNode from './BaseNode.vue';
import { NodeInput } from './shared';
import { TransferIcon } from './icons';

const chatCenterValue = ref('');

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
