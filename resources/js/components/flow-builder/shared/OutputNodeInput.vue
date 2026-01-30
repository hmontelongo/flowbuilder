<template>
    <div ref="inputEl" class="output-node-input flex items-center gap-2">
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
import { ref, inject, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    buttonId: {
        type: String,
        default: null,
    },
});

defineEmits(['update:modelValue', 'action']);

const inputEl = ref(null);

// Register element ref with parent for position calculations
const buttonElementRegistry = inject('buttonElementRegistry', null);

onMounted(() => {
    if (buttonElementRegistry && props.buttonId && inputEl.value) {
        buttonElementRegistry.register(props.buttonId, inputEl.value);
    }
});

onUnmounted(() => {
    if (buttonElementRegistry && props.buttonId) {
        buttonElementRegistry.unregister(props.buttonId);
    }
});

// Update registration if buttonId changes
watch(() => props.buttonId, (newId, oldId) => {
    if (buttonElementRegistry) {
        if (oldId) buttonElementRegistry.unregister(oldId);
        if (newId && inputEl.value) {
            buttonElementRegistry.register(newId, inputEl.value);
        }
    }
});
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
