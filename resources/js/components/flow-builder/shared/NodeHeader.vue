<template>
    <div class="flex items-center gap-2 px-1" :style="{ width: headerWidth }">
        <!-- Icon chip -->
        <div
            class="flex items-center justify-center shrink-0"
            :style="{
                width: `${chipSize}px`,
                height: `${chipSize}px`,
                backgroundColor: iconColor,
                borderRadius: `${chipRadius}px`,
                padding: '4px',
            }"
        >
            <slot name="icon" />
        </div>
        <span class="node-text-sm flex-1 font-medium text-left overflow-hidden text-ellipsis whitespace-nowrap">{{ label }}</span>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    iconColor: {
        type: String,
        required: true,
    },
    headerWidth: {
        type: String,
        default: '308px',
    },
    chipSize: {
        type: Number,
        default: 24,
    },
    chipRadius: {
        type: Number,
        default: 8,
    },
});
</script>
