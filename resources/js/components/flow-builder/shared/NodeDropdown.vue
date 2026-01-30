<template>
    <div ref="triggerRef" class="relative">
        <!-- Trigger button -->
        <div
            class="node-input flex items-center gap-2 cursor-pointer"
            @click.stop="toggleDropdown"
            @mousedown.stop
        >
            <span
                class="node-text-xs flex-1 text-left min-w-0"
                :class="{ 'node-text-placeholder': !selectedOption }"
            >{{ selectedOption ? selectedOption.label : placeholder }}</span>
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none" style="color: var(--color-fb-neutral-700);">
                <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <!-- Dropdown menu (teleported to body for proper z-index) -->
        <Teleport to="body">
            <div
                v-if="isOpen"
                class="node-dropdown-menu fixed flex flex-col gap-1"
                :style="dropdownStyle"
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
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';

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

const triggerRef = ref(null);
const isOpen = ref(false);
const selectedValue = ref(props.modelValue);
const dropdownPosition = ref({ top: 0, left: 0, width: 0 });

const allOptions = computed(() => {
    return props.sections.flatMap(section => section.options);
});

const selectedOption = computed(() => {
    return allOptions.value.find(opt => opt.value === selectedValue.value) || null;
});

const dropdownStyle = computed(() => ({
    top: `${dropdownPosition.value.top}px`,
    left: `${dropdownPosition.value.left}px`,
    width: `${dropdownPosition.value.width}px`,
    zIndex: 99999,
}));

const updatePosition = () => {
    if (triggerRef.value) {
        const rect = triggerRef.value.getBoundingClientRect();
        dropdownPosition.value = {
            top: rect.bottom + 4,
            left: rect.left,
            width: rect.width,
        };
    }
};

const toggleDropdown = () => {
    if (!isOpen.value) {
        updatePosition();
    }
    isOpen.value = !isOpen.value;
};

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
