<template>
    <Handle
        :id="id"
        :type="type"
        :position="position"
        :connectable="true"
        class="!relative !transform-none"
        :class="{ 'connected-handle': connected }"
        :style="handleStyles"
    />
</template>

<script setup>
import { computed, inject } from 'vue';
import { Handle, Position } from '@vue-flow/core';

const props = defineProps({
    id: {
        type: String,
        default: undefined,
    },
    type: {
        type: String,
        required: true,
        validator: (value) => ['source', 'target'].includes(value),
    },
    side: {
        type: String,
        required: true,
        validator: (value) => ['left', 'right', 'top', 'bottom'].includes(value),
    },
    variant: {
        type: String,
        default: 'normal',
        validator: (value) => ['normal', 'error'].includes(value),
    },
    connected: {
        type: Boolean,
        default: false,
    },
});

const position = computed(() => {
    const positionMap = {
        left: Position.Left,
        right: Position.Right,
        top: Position.Top,
        bottom: Position.Bottom,
    };
    return positionMap[props.side];
});

const handleStyles = computed(() => {
    const styles = {};

    if (props.variant === 'error') {
        styles.borderColor = 'var(--color-fb-node-border-error)';
        if (props.connected) {
            styles.background = 'var(--color-fb-node-border-error)';
        }
    } else if (props.connected) {
        styles.background = 'var(--color-fb-node-border-edit)';
    }

    return styles;
});
</script>

<style>
/* Connected handle styling */
.connected-handle {
    background: var(--color-fb-node-border-edit) !important;
}
</style>
