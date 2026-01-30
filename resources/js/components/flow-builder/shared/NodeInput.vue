<template>
    <div class="node-input flex items-center gap-2">
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
        <!-- Search icon -->
        <svg v-if="icon === 'search'" class="w-4 h-4 shrink-0" viewBox="0 0 14 14" fill="#364153">
            <path d="M13.5177 12.8106L9.74165 9.0346C10.649 7.94528 11.1015 6.54808 11.0049 5.13366C10.9084 3.71923 10.2702 2.39649 9.22323 1.44058C8.17625 0.484678 6.80105 -0.0307847 5.3837 0.00142333C3.96635 0.0336313 2.61598 0.61103 1.61351 1.61351C0.61103 2.61598 0.0336313 3.96635 0.00142333 5.3837C-0.0307847 6.80105 0.484678 8.17625 1.44058 9.22323C2.39649 10.2702 3.71923 10.9084 5.13366 11.0049C6.54808 11.1015 7.94528 10.649 9.0346 9.74165L12.8106 13.5177L13.5177 12.8106ZM1.0177 5.5177C1.0177 4.62768 1.28162 3.75765 1.77608 3.01763C2.27055 2.27761 2.97335 1.70083 3.79562 1.36024C4.61789 1.01964 5.52269 0.930529 6.3956 1.10416C7.26852 1.2778 8.07034 1.70638 8.69968 2.33572C9.32901 2.96505 9.7576 3.76688 9.93123 4.63979C10.1049 5.5127 10.0157 6.4175 9.67515 7.23977C9.33456 8.06204 8.75778 8.76484 8.01776 9.25931C7.27774 9.75378 6.40771 10.0177 5.5177 10.0177C4.32463 10.0164 3.1808 9.54184 2.33718 8.69822C1.49355 7.85459 1.01902 6.71076 1.0177 5.5177Z"/>
        </svg>
        <!-- Chevron icon -->
        <svg v-else-if="icon === 'chevron'" class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
            <path d="M4 6L8 10L12 6" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <!-- Plus/Add icon -->
        <button
            v-else-if="icon === 'add'"
            type="button"
            class="w-4 h-4 shrink-0 flex items-center justify-center hover:bg-gray-100 rounded transition-colors"
            @click.stop="$emit('action')"
        >
            <svg class="w-3 h-3" viewBox="0 0 11 11" fill="#161616">
                <path d="M6.1875 4.8125V0H4.8125V4.8125H0V6.1875H4.8125V11H6.1875V6.1875H11V4.8125H6.1875Z"/>
            </svg>
        </button>
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
    icon: {
        type: String,
        default: null,
        validator: (value) => [null, 'search', 'chevron', 'add'].includes(value),
    },
});

defineEmits(['update:modelValue', 'action']);
</script>

<style scoped>
input::placeholder {
    color: var(--color-fb-input-placeholder);
}
</style>
