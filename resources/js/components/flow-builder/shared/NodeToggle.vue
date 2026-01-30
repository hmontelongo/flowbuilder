<template>
    <div
        class="node-toggle flex items-center gap-1 cursor-pointer"
        @click.stop="toggle"
        @mousedown.stop
    >
        <!-- Toggle switch -->
        <div
            class="relative flex items-center rounded-full p-1 transition-colors"
            :style="{
                width: '32px',
                height: '16px',
                backgroundColor: modelValue ? 'var(--color-fb-text-link)' : 'var(--color-fb-neutral-500)',
            }"
        >
            <div
                class="absolute bg-white rounded-full transition-transform"
                :style="{
                    width: '8px',
                    height: '8px',
                    transform: modelValue ? 'translateX(16px)' : 'translateX(0)',
                }"
            ></div>
        </div>
        <!-- Label -->
        <span v-if="label" class="node-text-xs">{{ label }}</span>
    </div>
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
