<template>
    <div class="relative shrink-0">
        <div
            class="icon-chip flex items-center justify-center transition-all duration-150"
            :class="{ 'cursor-pointer': clickable }"
            :style="{
                width: `${size}px`,
                height: `${size}px`,
                backgroundColor: color,
                borderRadius: `${radius}px`,
                padding: '4px',
            }"
        >
            <slot />
        </div>
        <!-- Error indicator dot -->
        <div
            v-if="showError"
            class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full border border-white"
            :style="{ backgroundColor: errorColor }"
        ></div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    color: {
        type: String,
        required: true,
    },
    size: {
        type: Number,
        default: 24,
    },
    radius: {
        type: Number,
        default: 8,
    },
    clickable: {
        type: Boolean,
        default: false,
    },
    showError: {
        type: Boolean,
        default: false,
    },
    errorLevel: {
        type: String,
        default: 'warning', // 'error' | 'warning'
    },
});

const errorColor = computed(() => {
    return props.errorLevel === 'error'
        ? 'var(--color-fb-node-border-error)'
        : 'var(--color-fb-node-border-warning)';
});
</script>

<style scoped>
.icon-chip.cursor-pointer:hover {
    filter: brightness(0.92);
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.08);
}
</style>
