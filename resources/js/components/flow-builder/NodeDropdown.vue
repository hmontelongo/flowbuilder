<template>
    <div class="relative">
        <!-- Trigger button -->
        <div
            class="node-input flex items-center gap-2 cursor-pointer"
            @click.stop="isOpen = !isOpen"
            @mousedown.stop
        >
            <span
                class="node-text-xs flex-1 text-left min-w-0"
                :class="{ 'node-text-placeholder': !selectedOption }"
            >{{ selectedOption ? selectedOption.label : placeholder }}</span>
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 10 5.7" fill="#1e2939">
                <path d="M5 5.7L0 0.7L0.7 0L5 4.3L9.3 0L10 0.7L5 5.7Z"/>
            </svg>
        </div>

        <!-- Dropdown menu -->
        <div
            v-if="isOpen"
            class="node-dropdown-menu absolute left-0 right-0 flex flex-col gap-1"
            style="top: calc(100% + 4px); z-index: 9999;"
            @click.stop
            @mousedown.stop
        >
            <template v-for="(section, index) in sections" :key="section.label">
                <!-- Divider (between sections) -->
                <div v-if="index > 0" class="node-dropdown-divider"></div>

                <!-- Section header -->
                <div class="node-dropdown-header">{{ section.label }}</div>

                <!-- Options -->
                <div
                    v-for="option in section.options"
                    :key="option.value"
                    class="node-dropdown-option"
                    :class="{ 'is-selected': selectedValue === option.value }"
                    @click="selectOption(option)"
                >{{ option.label }}</div>
            </template>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Select an option',
    },
    sections: {
        type: Array,
        required: true,
    },
});

const isOpen = ref(false);
const selectedValue = ref(props.modelValue);

const allOptions = computed(() => {
    return props.sections.flatMap(section => section.options);
});

const selectedOption = computed(() => {
    return allOptions.value.find(opt => opt.value === selectedValue.value) || null;
});

const selectOption = (option) => {
    selectedValue.value = option.value;
    emit('update:modelValue', option.value);
    isOpen.value = false;
};

const handleClickOutside = () => {
    if (isOpen.value) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

