<template>
    <div class="node-card-wrapper relative flex items-center justify-center">
        <!-- Input connector with border notch -->
        <div
            v-if="$slots['input-connector']"
            class="absolute flex items-center justify-center"
            :style="{ left: '-4px', top: `${connectorOffset}px` }"
        >
            <!-- Semi-circle border (left side - shows right half) -->
            <div class="node-connector-notch">
                <div
                    class="node-connector-notch-inner node-connector-notch-left"
                    :class="{ 'node-connector-notch-edit': state.mode === 'edit', 'node-connector-notch-error': state.mode === 'error' }"
                ></div>
            </div>
            <slot name="input-connector" />
        </div>

        <!-- Card container -->
        <div
            class="node-card bg-white flex flex-col"
            :style="cardStyles"
        >
            <slot />
        </div>

        <!-- Output connector with border notch -->
        <div
            v-if="$slots['output-connector']"
            class="absolute flex items-center justify-center"
            :style="{ right: '-4px', top: `${connectorOffset}px` }"
        >
            <!-- Semi-circle border (right side - shows left half) -->
            <div class="node-connector-notch">
                <div
                    class="node-connector-notch-inner node-connector-notch-right"
                    :class="{ 'node-connector-notch-edit': state.mode === 'edit', 'node-connector-notch-error': state.mode === 'error' }"
                ></div>
            </div>
            <slot name="output-connector" />
        </div>

        <!-- Additional output connectors slot (for nodes with multiple outputs) -->
        <slot name="extra-connectors" />
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    state: {
        type: Object,
        default: () => ({ mode: 'normal' }),
        validator: (value) => {
            const validModes = ['normal', 'edit', 'readonly', 'error'];
            const validErrorLevels = ['warning', 'error'];
            if (!validModes.includes(value.mode)) return false;
            if (value.mode === 'error' && value.errorLevel && !validErrorLevels.includes(value.errorLevel)) return false;
            return true;
        },
    },
    width: {
        type: String,
        default: '300px',
    },
    padding: {
        type: String,
        default: '16px 8px',
    },
    borderRadius: {
        type: String,
        default: '8px',
    },
    connectorOffset: {
        type: Number,
        default: 12,
    },
});

const borderColor = computed(() => {
    switch (props.state.mode) {
        case 'edit':
            return 'var(--color-fb-node-border-edit)';
        case 'error':
            return props.state.errorLevel === 'error' ? 'var(--color-fb-node-border-error)' : 'var(--color-fb-node-border-warning)';
        case 'readonly':
        case 'normal':
        default:
            return 'var(--color-fb-node-border-normal)';
    }
});

const backgroundColor = computed(() => {
    return props.state.mode === 'readonly' ? 'var(--color-fb-neutral-50)' : 'var(--color-fb-node-bg-normal)';
});

const cardStyles = computed(() => ({
    width: props.width,
    border: `1px solid ${borderColor.value}`,
    borderRadius: props.borderRadius,
    padding: props.padding,
    backgroundColor: backgroundColor.value,
    opacity: props.state.mode === 'readonly' ? '0.7' : '1',
}));
</script>

<style scoped>
/* Smooth transition for hover state */
.node-card {
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

/* Hover state - blue border for interactive feedback */
.node-card:hover {
    border-color: var(--color-fb-node-border-edit, #3866ff) !important;
}
</style>
