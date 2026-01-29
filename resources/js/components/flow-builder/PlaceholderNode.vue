<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="white">
                <rect x="2" y="2" width="12" height="12" rx="2" stroke="white" stroke-width="1" fill="none"/>
                <path d="M5 8H11M8 5V11" stroke="white" stroke-width="1.5"/>
            </svg>
        </template>

        <div class="flex items-center justify-center w-full py-4">
            <span class="node-text-muted text-center">Próximamente</span>
        </div>
    </BaseNode>
</template>

<script setup>
import BaseNode from './BaseNode.vue';

const nodeConfig = {
    header: {
        iconColor: '#9ca3af',
    },
    card: {
        padding: '8px',
        borderRadius: '8px',
    },
    connectors: {
        input: true,
        output: true,
    },
};
</script>
