<template>
    <div class="group flex items-center gap-2 px-1" :style="{ width: headerWidth }">
        <!-- Icon chip with error indicator and optional tooltip -->
        <NodeTooltip
            v-if="tooltip"
            :title="tooltip.title"
            :description="tooltip.description"
            :link-text="tooltip.linkText"
            :link-url="tooltip.linkUrl"
        >
            <div class="relative shrink-0">
                <div
                    class="icon-chip flex items-center justify-center cursor-pointer transition-all duration-150"
                    :style="{
                        width: `${chipSize}px`,
                        height: `${chipSize}px`,
                        backgroundColor: iconColor,
                        borderRadius: `${chipRadius}px`,
                        padding: '4px',
                    }"
                >
                    <slot name="icon" />
                </div>
                <!-- Error indicator dot -->
                <div
                    v-if="state.mode === 'error'"
                    class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full border border-white"
                    :style="{ backgroundColor: errorIndicatorColor }"
                ></div>
            </div>
        </NodeTooltip>

        <!-- Icon chip without tooltip -->
        <div v-else class="relative shrink-0">
            <div
                class="flex items-center justify-center"
                :style="{
                    width: `${chipSize}px`,
                    height: `${chipSize}px`,
                    backgroundColor: iconColor,
                    borderRadius: `${chipRadius}px`,
                    padding: '4px',
                }"
            >
                <slot name="icon" />
            </div>
            <!-- Error indicator dot -->
            <div
                v-if="state.mode === 'error'"
                class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full border border-white"
                :style="{ backgroundColor: errorIndicatorColor }"
            ></div>
        </div>

        <span class="node-text-sm flex-1 font-medium text-left overflow-hidden text-ellipsis whitespace-nowrap">{{ label }}</span>
        <!-- Action buttons: Duplicate + Delete (visible on hover) -->
        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
            <!-- Duplicate button -->
            <button
                v-if="showDuplicate"
                type="button"
                class="w-4 h-4 flex items-center justify-center hover:bg-gray-100 rounded transition-colors"
                title="Duplicar"
                @click.stop="$emit('duplicate')"
            >
                <svg class="w-2.5 h-2.5" viewBox="0 0 10 10" fill="none">
                    <rect x="3" y="3" width="6.5" height="6.5" rx="1" stroke="#1e2939" stroke-width="1"/>
                    <path d="M2 7V1.5C2 1.22 2.22 1 2.5 1H7" stroke="#1e2939" stroke-width="1" stroke-linecap="round"/>
                </svg>
            </button>
            <!-- Delete button (X icon) -->
            <button
                v-if="showDelete"
                type="button"
                class="w-4 h-4 flex items-center justify-center hover:bg-gray-100 rounded transition-colors"
                title="Eliminar"
                @click.stop="$emit('delete')"
            >
                <svg class="w-2 h-2" viewBox="0 0 8 8" :style="{ fill: 'var(--color-fb-neutral-800)' }">
                    <path d="M7.5 1.205L6.795 0.5L4 3.295L1.205 0.5L0.5 1.205L3.295 4L0.5 6.795L1.205 7.5L4 4.705L6.795 7.5L7.5 6.795L4.705 4L7.5 1.205Z"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import NodeTooltip from './NodeTooltip.vue';

const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    iconColor: {
        type: String,
        required: true,
    },
    headerWidth: {
        type: String,
        default: '308px',
    },
    chipSize: {
        type: Number,
        default: 24,
    },
    chipRadius: {
        type: Number,
        default: 8,
    },
    state: {
        type: Object,
        default: () => ({ mode: 'normal' }),
    },
    showDelete: {
        type: Boolean,
        default: true,
    },
    showDuplicate: {
        type: Boolean,
        default: true,
    },
    tooltip: {
        type: Object,
        default: null,
        // { title, description, linkText, linkUrl }
    },
});

defineEmits(['delete', 'duplicate']);

const errorIndicatorColor = computed(() => {
    return props.state.errorLevel === 'error'
        ? 'var(--color-fb-node-border-error)'
        : 'var(--color-fb-node-border-warning)';
});
</script>

<style scoped>
/* Icon chip hover effect to indicate it's clickable */
.icon-chip:hover {
    filter: brightness(0.92);
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.08);
}
</style>
