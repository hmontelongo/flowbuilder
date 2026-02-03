<template>
    <div class="group flex items-center gap-2 px-1" :style="{ width: headerWidth }">
        <!-- Icon chip with optional tooltip -->
        <NodeTooltip
            v-if="tooltip"
            :title="tooltip.title"
            :description="tooltip.description"
            :link-text="tooltip.linkText"
            :link-url="tooltip.linkUrl"
        >
            <IconChip
                :color="iconColor"
                :size="chipSize"
                :radius="chipRadius"
                :clickable="true"
                :show-error="state.mode === 'error'"
                :error-level="state.errorLevel"
            >
                <slot name="icon" />
            </IconChip>
        </NodeTooltip>

        <!-- Icon chip without tooltip -->
        <IconChip
            v-else
            :color="iconColor"
            :size="chipSize"
            :radius="chipRadius"
            :show-error="state.mode === 'error'"
            :error-level="state.errorLevel"
        >
            <slot name="icon" />
        </IconChip>

        <span class="node-text-sm flex-1 font-medium text-left overflow-hidden text-ellipsis whitespace-nowrap">{{ label }}</span>
        <!-- Action buttons: Duplicate + Delete (visible on hover, matching Tag modifier style) -->
        <div class="flex items-center gap-1 shrink-0">
            <!-- Duplicate button -->
            <button
                v-if="showDuplicate"
                type="button"
                class="header-action-btn header-action-duplicate"
                title="Duplicar"
                @click.stop="$emit('duplicate')"
            >
                <svg class="w-2.5 h-2.5" viewBox="0 0 10 10" fill="none">
                    <rect x="3" y="3" width="6.5" height="6.5" rx="1" stroke="currentColor" stroke-width="1"/>
                    <path d="M2 7V1.5C2 1.22 2.22 1 2.5 1H7" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                </svg>
            </button>
            <!-- Delete button (X icon) -->
            <button
                v-if="showDelete"
                type="button"
                class="header-action-btn header-action-delete"
                title="Eliminar"
                @click.stop="$emit('delete')"
            >
                <svg class="w-2.5 h-2.5" viewBox="0 0 8 8" fill="currentColor">
                    <path d="M7.5 1.205L6.795 0.5L4 3.295L1.205 0.5L0.5 1.205L3.295 4L0.5 6.795L1.205 7.5L4 4.705L6.795 7.5L7.5 6.795L4.705 4L7.5 1.205Z"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import NodeTooltip from './NodeTooltip.vue';
import IconChip from './IconChip.vue';

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
        default: '300px',
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
</script>

<style scoped>
/* Action buttons - matching Tag modifier close button behavior */
.header-action-btn {
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: var(--color-fb-neutral-400, #9ca3af);
    border-radius: 4px;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.15s ease, background-color 0.15s ease, color 0.15s ease;
}

/* Show buttons on parent hover */
.group:hover .header-action-btn {
    opacity: 1;
}

/* Delete button hover - red theme */
.header-action-delete:hover {
    background: #fef2f2;
    color: var(--color-fb-node-border-error, #ef4444);
}

/* Duplicate button hover - blue theme */
.header-action-duplicate:hover {
    background: #eff6ff;
    color: var(--color-fb-node-border-edit, #3866ff);
}
</style>

