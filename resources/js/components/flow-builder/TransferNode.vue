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
import NodeInput from './NodeInput.vue';
import { TransferIcon } from './icons';

const chatCenterValue = ref('');

const nodeConfig = {
    header: {
        iconColor: '#d1345b',
        icon: TransferIcon,
    },
    card: {
        padding: '16px 8px',
        borderRadius: '8px',
    },
    connectors: {
        input: true,
        output: true,
    },
};
</script>
