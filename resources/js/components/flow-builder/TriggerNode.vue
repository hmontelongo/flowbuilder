<template>
    <div class="trigger-node flex flex-col items-center gap-2" style="font-family: 'Inter', sans-serif;">
        <!-- Header (outside card) -->
        <div class="flex items-center gap-2 px-1" style="width: 308px;">
            <!-- Icon chip: 24px (p-1 = 4px each side + 16px icon) -->
            <div
                class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                style="background-color: #34d1bf;"
            >
                <!-- Lightning icon - white fill -->
                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="white">
                    <path d="M8.5 1L3 9h4.5v6L13 7H8.5V1z"/>
                </svg>
            </div>
            <span
                class="flex-1 font-medium text-left overflow-hidden text-ellipsis whitespace-nowrap"
                style="font-size: 14px; line-height: 18px; color: #1e2939; letter-spacing: 0;"
            >{{ data.label }}</span>
        </div>

        <!-- White container with connectors -->
        <div class="relative flex items-center justify-center" style="padding-right: 4px;">
            <!-- Input connector with semi-circle indicator -->
            <div class="absolute flex flex-col items-start justify-center" style="left: 0; top: 12px;">
                <!-- Semi-circle indicator (behind) - left side -->
                <div class="absolute" style="width: 10px; height: 10px; left: 50%; top: 50%; transform: translate(-50%, -50%);">
                    <div
                        class="absolute"
                        style="top: 0; bottom: 0; left: 0; right: 50%; border: 1px solid #e5e7eb; border-right: none; border-radius: 5px 0 0 5px;"
                    ></div>
                </div>
                <Handle
                    type="target"
                    :position="Position.Left"
                    class="!relative !transform-none"
                />
            </div>

            <div
                class="bg-white"
                style="width: 300px; border: 1px solid #e5e7eb; border-radius: 8px 8px 4px 8px; padding: 16px 8px; margin-right: -4px;"
            >
                <!-- Custom dropdown -->
                <TriggerDropdown v-model="selectedAction" />
            </div>

            <!-- Output connector with semi-circle indicator -->
            <div class="absolute flex flex-col items-start justify-center" style="right: 0; top: 12px;">
                <!-- Semi-circle indicator (behind) - right side -->
                <div class="absolute" style="width: 10px; height: 10px; left: 50%; top: 50%; transform: translate(-50%, -50%);">
                    <div
                        class="absolute"
                        style="top: 0; bottom: 0; left: 50%; right: 0; border: 1px solid #e5e7eb; border-left: none; border-radius: 0 5px 5px 0;"
                    ></div>
                </div>
                <Handle
                    type="source"
                    :position="Position.Right"
                    class="!relative !transform-none"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Handle, Position } from '@vue-flow/core';
import TriggerDropdown from './TriggerDropdown.vue';

defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const selectedAction = ref('');
</script>

<style scoped>
.trigger-node {
    position: relative;
}
</style>
