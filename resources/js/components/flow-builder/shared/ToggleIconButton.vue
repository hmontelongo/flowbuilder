<template>
    <button
        type="button"
        class="toggle-icon-btn"
        :class="{ 'is-active': modelValue }"
        :title="label"
        @click.stop="toggle"
        @mousedown.stop
    >
        <slot />
    </button>
</template>

<script setup>
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const toggle = () => {
    emit('update:modelValue', !props.modelValue);
};
</script>

<style scoped>
.toggle-icon-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    padding: 2px;
    border: 1px solid transparent;
    border-radius: 4px;
    background-color: transparent;
    color: var(--color-fb-neutral-800);
    cursor: pointer;
    transition: all 0.15s ease;
}

.toggle-icon-btn:hover {
    background-color: var(--color-fb-neutral-100);
}

.toggle-icon-btn.is-active {
    border-color: var(--color-fb-neutral-700);
    background-color: var(--color-fb-neutral-0);
}

.toggle-icon-btn :deep(svg) {
    width: 16px;
    height: 16px;
}
</style>
