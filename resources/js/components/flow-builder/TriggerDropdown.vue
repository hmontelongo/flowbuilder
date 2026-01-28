<template>
    <div class="relative" style="font-family: 'Inter', sans-serif;">
        <!-- Trigger button (styled like select) -->
        <div
            class="flex items-center gap-2 bg-white cursor-pointer"
            style="border: 1px solid #99a1af; border-radius: 4px; padding: 4px;"
            @click.stop="isOpen = !isOpen"
            @mousedown.stop
        >
            <span
                class="flex-1 text-left"
                :style="{
                    fontSize: '12px',
                    lineHeight: '16px',
                    color: selectedOption ? '#1e2939' : '#99a1af',
                    letterSpacing: '0',
                    minWidth: 0,
                }"
            >{{ selectedOption ? selectedOption.label : 'Selecciona una acción para iniciar el flujo' }}</span>
            <!-- Chevron down icon - #161616 -->
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
                <path d="M4 6l4 4 4-4" stroke="#161616" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <!-- Dropdown menu -->
        <div
            v-if="isOpen"
            class="absolute left-0 right-0 z-50 flex flex-col"
            style="
                top: calc(100% + 4px);
                background-color: #f9fafb;
                border-radius: 8px;
                padding: 4px 8px 16px 8px;
                gap: 4px;
            "
            @click.stop
            @mousedown.stop
        >
            <!-- GENERAL Section -->
            <div
                style="
                    font-size: 9px;
                    line-height: 14px;
                    color: #7e7e7e;
                    text-transform: uppercase;
                    padding: 3px 8px;
                    letter-spacing: 0;
                "
            >General</div>
            <div
                v-for="option in generalOptions"
                :key="option.value"
                class="cursor-pointer"
                :style="{
                    fontSize: '12px',
                    lineHeight: '14px',
                    color: '#404040',
                    padding: '3px 8px',
                    borderRadius: '2px',
                    letterSpacing: '0',
                    backgroundColor: selectedValue === option.value ? '#e5e7eb' : 'transparent',
                }"
                @click="selectOption(option)"
                @mouseenter="$event.target.style.backgroundColor = selectedValue === option.value ? '#e5e7eb' : '#f3f4f6'"
                @mouseleave="$event.target.style.backgroundColor = selectedValue === option.value ? '#e5e7eb' : 'transparent'"
            >{{ option.label }}</div>

            <!-- Divider -->
            <div style="background-color: #d1d5dc; height: 1px; border-radius: 4px; margin: 4px 0;"></div>

            <!-- DESDE SELLIA Section -->
            <div
                style="
                    font-size: 9px;
                    line-height: 14px;
                    color: #7e7e7e;
                    text-transform: uppercase;
                    padding: 3px 8px;
                    letter-spacing: 0;
                "
            >Desde Sellia</div>
            <div
                v-for="option in selliaOptions"
                :key="option.value"
                class="cursor-pointer"
                :style="{
                    fontSize: '12px',
                    lineHeight: '14px',
                    color: '#404040',
                    padding: '3px 8px',
                    borderRadius: '2px',
                    letterSpacing: '0',
                    backgroundColor: selectedValue === option.value ? '#e5e7eb' : 'transparent',
                }"
                @click="selectOption(option)"
                @mouseenter="$event.target.style.backgroundColor = selectedValue === option.value ? '#e5e7eb' : '#f3f4f6'"
                @mouseleave="$event.target.style.backgroundColor = selectedValue === option.value ? '#e5e7eb' : 'transparent'"
            >{{ option.label }}</div>

            <!-- Divider -->
            <div style="background-color: #d1d5dc; height: 1px; border-radius: 4px; margin: 4px 0;"></div>

            <!-- AVANZADO Section -->
            <div
                style="
                    font-size: 9px;
                    line-height: 14px;
                    color: #7e7e7e;
                    text-transform: uppercase;
                    padding: 3px 8px;
                    letter-spacing: 0;
                "
            >Avanzado</div>
            <div
                v-for="option in advancedOptions"
                :key="option.value"
                class="cursor-pointer"
                :style="{
                    fontSize: '12px',
                    lineHeight: '14px',
                    color: '#404040',
                    padding: '3px 8px',
                    borderRadius: '2px',
                    letterSpacing: '0',
                    backgroundColor: selectedValue === option.value ? '#e5e7eb' : 'transparent',
                }"
                @click="selectOption(option)"
                @mouseenter="$event.target.style.backgroundColor = selectedValue === option.value ? '#e5e7eb' : '#f3f4f6'"
                @mouseleave="$event.target.style.backgroundColor = selectedValue === option.value ? '#e5e7eb' : 'transparent'"
            >{{ option.label }}</div>
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
});

const isOpen = ref(false);
const selectedValue = ref(props.modelValue);

const generalOptions = [
    { value: 'keyword_message', label: 'Mensaje con palabra clave' },
    { value: 'any_message', label: 'Recibir cualquier mensaje' },
];

const selliaOptions = [
    { value: 'survey', label: 'Encuesta' },
    { value: 'template', label: 'Template' },
    { value: 'whatsapp_flow', label: 'WhatsApp Flow' },
];

const advancedOptions = [
    { value: 'regex', label: 'Expresión regular' },
];

const allOptions = [...generalOptions, ...selliaOptions, ...advancedOptions];

const selectedOption = computed(() => {
    return allOptions.find(opt => opt.value === selectedValue.value) || null;
});

const selectOption = (option) => {
    selectedValue.value = option.value;
    emit('update:modelValue', option.value);
    isOpen.value = false;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
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
