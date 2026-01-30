<template>
    <div
        class="output-button-input"
        :class="{ 'is-dragging': isDragging }"
        draggable="true"
        @dragstart="onDragStart"
        @dragend="onDragEnd"
        @dragover.prevent
        @drop="onDrop"
    >
        <!-- Drag handle -->
        <button
            type="button"
            class="drag-handle"
            @mousedown.stop
        >
            <svg class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor">
                <circle cx="3" cy="2" r="1"/>
                <circle cx="3" cy="6" r="1"/>
                <circle cx="3" cy="10" r="1"/>
                <circle cx="9" cy="2" r="1"/>
                <circle cx="9" cy="6" r="1"/>
                <circle cx="9" cy="10" r="1"/>
            </svg>
        </button>

        <!-- Input field -->
        <input
            :value="modelValue"
            type="text"
            :placeholder="placeholder"
            :maxlength="maxLength"
            class="button-text-input"
            @input="$emit('update:modelValue', $event.target.value)"
            @click.stop
            @mousedown.stop
        />

        <!-- Character counter -->
        <span class="char-counter">{{ remainingChars }}</span>

        <!-- Delete button -->
        <button
            type="button"
            class="delete-btn"
            @click.stop="$emit('delete')"
            @mousedown.stop
        >
            <svg class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor">
                <path d="M9.5 3L8.5 2L6 4.5L3.5 2L2.5 3L5 5.5L2.5 8L3.5 9L6 6.5L8.5 9L9.5 8L7 5.5L9.5 3Z"/>
            </svg>
        </button>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Texto del botón',
    },
    maxLength: {
        type: Number,
        default: 20,
    },
    index: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue', 'delete', 'reorder']);

const isDragging = ref(false);

const remainingChars = computed(() => props.maxLength - props.modelValue.length);

const onDragStart = (e) => {
    isDragging.value = true;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', props.index.toString());
};

const onDragEnd = () => {
    isDragging.value = false;
};

const onDrop = (e) => {
    const fromIndex = parseInt(e.dataTransfer.getData('text/plain'), 10);
    const toIndex = props.index;
    if (fromIndex !== toIndex) {
        emit('reorder', { from: fromIndex, to: toIndex });
    }
};
</script>

<style scoped>
.output-button-input {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px;
    border: 1px solid var(--color-fb-node-border-edit);
    border-radius: 4px;
    background-color: white;
    transition: border-color 0.15s ease, opacity 0.15s ease;
}

.output-button-input:focus-within {
    box-shadow: 0 0 0 1px var(--color-fb-node-border-edit);
}

.output-button-input.is-dragging {
    opacity: 0.5;
}

.drag-handle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    color: var(--color-fb-neutral-500);
    background: none;
    border: none;
    cursor: grab;
    flex-shrink: 0;
}

.drag-handle:active {
    cursor: grabbing;
}

.button-text-input {
    flex: 1;
    min-width: 0;
    font-size: var(--fb-text-xs);
    line-height: var(--fb-line-height-tight);
    color: var(--color-fb-input-text);
    background: transparent;
    border: none;
    outline: none;
}

.button-text-input::placeholder {
    color: var(--color-fb-input-placeholder);
}

.char-counter {
    font-size: var(--fb-text-xs);
    line-height: var(--fb-line-height-tight);
    color: var(--color-fb-text-muted);
    flex-shrink: 0;
}

.delete-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    color: var(--color-fb-neutral-500);
    background: none;
    border: none;
    cursor: pointer;
    flex-shrink: 0;
    border-radius: 2px;
    transition: background-color 0.15s ease, color 0.15s ease;
}

.delete-btn:hover {
    background-color: var(--color-fb-neutral-100);
    color: var(--color-fb-node-border-error);
}
</style>
