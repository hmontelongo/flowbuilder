<template>
    <button
        type="button"
        class="add-button-counter"
        :class="{ 'is-disabled': remaining <= 0 }"
        :disabled="remaining <= 0"
        :title="remaining > 0 ? `Agregar botón (${remaining} disponibles)` : 'Máximo de botones alcanzado'"
        @click.stop="$emit('add')"
        @mousedown.stop
    >
        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
            <rect x="2" y="5" width="12" height="6" rx="1" fill="none" stroke="currentColor" stroke-width="1.2"/>
            <rect x="5" y="7" width="6" height="2" rx="0.5"/>
        </svg>
        <span class="counter-number">{{ remaining }}</span>
    </button>
</template>

<script setup>
defineProps({
    remaining: {
        type: Number,
        required: true,
    },
});

defineEmits(['add']);
</script>

<style scoped>
.add-button-counter {
    display: flex;
    align-items: center;
    gap: 2px;
    padding: 4px;
    color: var(--color-fb-neutral-600);
    background-color: transparent;
    border: 1px solid var(--color-fb-button-border);
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.15s ease, border-color 0.15s ease;
}

.add-button-counter:hover:not(.is-disabled) {
    background-color: var(--color-fb-button-hover);
    border-color: var(--color-fb-neutral-500);
}

.add-button-counter.is-disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.counter-number {
    font-size: var(--fb-text-xs);
    line-height: 1;
    color: var(--color-fb-neutral-500);
}
</style>
