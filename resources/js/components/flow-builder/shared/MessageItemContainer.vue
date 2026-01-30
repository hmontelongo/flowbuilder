<template>
    <div
        class="message-item-container group/item"
        :class="{
            'is-dragging': isDragging,
            'is-drag-over': isDragOver
        }"
        draggable="true"
        @dragstart="onDragStart"
        @dragend="onDragEnd"
        @dragover.prevent="onDragOver"
        @dragleave="onDragLeave"
        @drop.prevent="onDrop"
    >
        <!-- Drag handle - visible on hover -->
        <button
            type="button"
            class="item-action opacity-0 group-hover/item:opacity-100"
            title="Arrastrar para reordenar"
            @mousedown.stop
        >
            <svg class="w-2 h-2" viewBox="0 0 8 8" fill="currentColor">
                <circle cx="2" cy="1" r="1"/>
                <circle cx="6" cy="1" r="1"/>
                <circle cx="2" cy="4" r="1"/>
                <circle cx="6" cy="4" r="1"/>
                <circle cx="2" cy="7" r="1"/>
                <circle cx="6" cy="7" r="1"/>
            </svg>
        </button>

        <!-- Content area -->
        <div class="message-item-content">
            <slot />
        </div>

        <!-- Close button - visible on hover -->
        <button
            type="button"
            class="item-action opacity-0 group-hover/item:opacity-100"
            title="Eliminar"
            @click.stop="$emit('remove')"
            @mousedown.stop
        >
            <svg class="w-2 h-2" viewBox="0 0 8 8" fill="currentColor">
                <path d="M7.5 1.205L6.795 0.5L4 3.295L1.205 0.5L0.5 1.205L3.295 4L0.5 6.795L1.205 7.5L4 4.705L6.795 7.5L7.5 6.795L4.705 4L7.5 1.205Z"/>
            </svg>
        </button>
    </div>
</template>

<script setup>
defineProps({
    isDragging: {
        type: Boolean,
        default: false,
    },
    isDragOver: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['remove', 'dragstart', 'dragend', 'dragover', 'dragleave', 'drop']);

const onDragStart = (e) => {
    e.dataTransfer.effectAllowed = 'move';
    emit('dragstart', e);
};

const onDragEnd = (e) => {
    emit('dragend', e);
};

const onDragOver = (e) => {
    e.dataTransfer.dropEffect = 'move';
    emit('dragover', e);
};

const onDragLeave = (e) => {
    emit('dragleave', e);
};

const onDrop = (e) => {
    emit('drop', e);
};
</script>

<style scoped>
.message-item-container {
    display: flex;
    align-items: flex-start;
    gap: var(--fb-space-xs);
    background-color: var(--color-fb-input-bg);
    border-radius: var(--fb-space-xs);
    padding: var(--fb-space-s);
    transition: opacity 0.15s ease, background-color 0.15s ease;
}

.message-item-container.is-dragging {
    opacity: 0.5;
}

.message-item-container.is-drag-over {
    background-color: var(--color-fb-neutral-100);
    outline: 2px dashed var(--color-fb-neutral-400);
    outline-offset: -2px;
}

.message-item-content {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: var(--fb-space-xs);
}

/* Shared style for drag handle and close button */
.item-action {
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-fb-neutral-500);
    background: transparent;
    border: none;
    border-radius: 2px;
    cursor: pointer;
    transition: opacity 0.15s ease, color 0.15s ease, background-color 0.15s ease;
}

.item-action:hover {
    color: var(--color-fb-neutral-700);
    background-color: var(--color-fb-neutral-200);
}

.item-action:first-child {
    cursor: grab;
}

.item-action:first-child:active {
    cursor: grabbing;
}
</style>
