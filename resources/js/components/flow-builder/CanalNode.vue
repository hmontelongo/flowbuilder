<template>
    <div class="canal-node flex flex-col items-center gap-2" style="font-family: 'Inter', sans-serif;">
        <!-- Header (outside card) -->
        <div class="flex items-center gap-2 px-1" style="width: 308px;">
            <!-- Icon chip: 24px (p-1 = 4px each side + 16px icon) -->
            <div
                class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                style="background-color: #fec601;"
            >
                <!-- Chart/Relationship icon -->
                <svg class="w-4 h-4" viewBox="0 0 16 16" fill="white">
                    <circle cx="4" cy="4" r="2"/>
                    <circle cx="12" cy="4" r="2"/>
                    <circle cx="4" cy="12" r="2"/>
                    <circle cx="12" cy="12" r="2"/>
                    <path d="M6 4h4M4 6v4M12 6v4M6 12h4" stroke="white" stroke-width="1.5" fill="none"/>
                </svg>
            </div>
            <span
                class="flex-1 font-medium text-left overflow-hidden text-ellipsis whitespace-nowrap"
                style="font-size: 14px; line-height: 18px; color: #1e2939; letter-spacing: 0;"
            >{{ data.label }}</span>
        </div>

        <!-- White container with connector -->
        <div class="relative flex items-center justify-center" style="padding-right: 4px;">
            <div
                class="bg-white"
                style="width: 300px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 8px; margin-right: -4px;"
            >
                <!-- Input field - interactive -->
                <div
                    class="flex items-center gap-2 bg-white"
                    style="border: 1px solid #99a1af; border-radius: 4px; padding: 4px;"
                >
                    <input
                        v-model="searchValue"
                        type="text"
                        placeholder="Buscar un canal"
                        class="flex-1 outline-none bg-transparent"
                        style="font-size: 12px; line-height: 16px; color: #1e2939; letter-spacing: 0; min-width: 0;"
                        @click.stop
                        @mousedown.stop
                    />
                    <!-- Search icon - #161616 -->
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 16 16" fill="none">
                        <circle cx="6.5" cy="6.5" r="4.5" stroke="#161616" stroke-width="1.5"/>
                        <path d="M10 10l4 4" stroke="#161616" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <!-- Output connector -->
            <div class="absolute flex items-center justify-center" style="right: 0; top: 12px;">
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

defineProps({
    data: {
        type: Object,
        required: true,
    },
});

const searchValue = ref('');
</script>

<style scoped>
.canal-node {
    position: relative;
}

.canal-node input::placeholder {
    color: #99a1af;
}
</style>
