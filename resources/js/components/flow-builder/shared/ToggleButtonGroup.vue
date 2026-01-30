<template>
    <div class="toggle-button-group">
        <button
            v-for="option in options"
            :key="option.id"
            type="button"
            class="toggle-btn"
            :class="{ 'is-active': modelValue === option.id }"
            :title="option.label"
            @click.stop="toggleOption(option.id)"
            @mousedown.stop
        >
            <component :is="option.icon" />
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: String,
        default: null,
    },
    options: {
        type: Array,
        required: true,
        // Each option: { id: string, icon: Component, label: string }
    },
});

const emit = defineEmits(['update:modelValue']);

const toggleOption = (optionId) => {
    // If clicking the active option, deselect it (set to null)
    // Otherwise, select the new option
    emit('update:modelValue', props.modelValue === optionId ? null : optionId);
};
</script>

<style scoped>
.toggle-button-group {
    display: flex;
    align-items: center;
    padding: 2px;
    background-color: var(--color-fb-neutral-100);
    border: 1px solid var(--color-fb-neutral-400);
    border-radius: 4px;
}

.toggle-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    padding: 2px;
    border: none;
    border-radius: 4px;
    background-color: transparent;
    color: var(--color-fb-neutral-800);
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.toggle-btn:hover {
    background-color: var(--color-fb-neutral-200);
}

.toggle-btn.is-active {
    background-color: var(--color-fb-neutral-0);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.toggle-btn :deep(svg) {
    width: 12px;
    height: 12px;
}
</style>
