<template>
    <div class="output-node-input flex items-center gap-2">
        <!-- Left icon slot -->
        <slot name="leftIcon" />
        <input
            :value="modelValue"
            type="text"
            :placeholder="placeholder"
            class="node-text-xs flex-1 outline-none bg-transparent min-w-0"
            @input="$emit('update:modelValue', $event.target.value)"
            @keydown.enter.prevent="$emit('action')"
            @click.stop
            @mousedown.stop
        />
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
});

defineEmits(['update:modelValue', 'action']);
</script>

<style scoped>
.output-node-input {
    border: 1px solid var(--color-fb-node-border-edit);
    border-radius: 4px;
    padding: 4px;
    background: white;
}

.output-node-input:focus-within {
    box-shadow: 0 0 0 1px var(--color-fb-node-border-edit);
}

input::placeholder {
    color: var(--color-fb-input-placeholder);
}
</style>
