<template>
    <div
        class="absolute flex items-center justify-center"
        :style="positionStyle"
    >
        <!-- Semi-circle border notch -->
        <div class="node-connector-notch">
            <div
                class="node-connector-notch-inner"
                :class="notchClasses"
            ></div>
        </div>
        <NodeConnector
            :id="id"
            :type="type"
            :side="side"
            :variant="variant"
            :connected="connected"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import NodeConnector from './NodeConnector.vue';

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'source',
        validator: (value) => ['source', 'target'].includes(value),
    },
    side: {
        type: String,
        default: 'right',
        validator: (value) => ['left', 'right'].includes(value),
    },
    variant: {
        type: String,
        default: 'normal',
        validator: (value) => ['normal', 'edit', 'error'].includes(value),
    },
    connected: {
        type: Boolean,
        default: false,
    },
    // Position can be specified as top or bottom offset
    top: {
        type: [Number, String],
        default: null,
    },
    bottom: {
        type: [Number, String],
        default: null,
    },
});

const positionStyle = computed(() => {
    const style = {};

    // Horizontal position based on side
    if (props.side === 'right') {
        style.right = '-4px';
    } else {
        style.left = '-4px';
    }

    // Vertical position
    if (props.top !== null) {
        style.top = typeof props.top === 'number' ? `${props.top}px` : props.top;
    } else if (props.bottom !== null) {
        style.bottom = typeof props.bottom === 'number' ? `${props.bottom}px` : props.bottom;
    }

    return style;
});

const notchClasses = computed(() => {
    const classes = [];

    // Side-specific notch class
    classes.push(props.side === 'right' ? 'node-connector-notch-right' : 'node-connector-notch-left');

    // Variant-specific notch class
    if (props.variant === 'edit') {
        classes.push('node-connector-notch-edit');
    } else if (props.variant === 'error') {
        classes.push('node-connector-notch-error');
    }

    return classes;
});
</script>
