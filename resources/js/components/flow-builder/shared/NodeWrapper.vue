<template>
    <div
        class="node-wrapper relative"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Hover actions (delete + duplicate) -->
        <div
            v-show="isHovered"
            class="absolute flex items-center gap-1 z-10"
            style="top: -4px; right: -4px;"
        >
            <button
                type="button"
                class="node-button-icon bg-white transition-colors"
                title="Duplicar"
                @click.stop="$emit('duplicate')"
            >
                <svg class="w-3 h-3" viewBox="0 0 12 12" fill="#364153">
                    <path d="M10 4H6C5.46957 4 4.96086 4.21071 4.58579 4.58579C4.21071 4.96086 4 5.46957 4 6V10C4 10.5304 4.21071 11.0391 4.58579 11.4142C4.96086 11.7893 5.46957 12 6 12H10C10.5304 12 11.0391 11.7893 11.4142 11.4142C11.7893 11.0391 12 10.5304 12 10V6C12 5.46957 11.7893 4.96086 11.4142 4.58579C11.0391 4.21071 10.5304 4 10 4ZM11 10C11 10.2652 10.8946 10.5196 10.7071 10.7071C10.5196 10.8946 10.2652 11 10 11H6C5.73478 11 5.48043 10.8946 5.29289 10.7071C5.10536 10.5196 5 10.2652 5 10V6C5 5.73478 5.10536 5.48043 5.29289 5.29289C5.48043 5.10536 5.73478 5 6 5H10C10.2652 5 10.5196 5.10536 10.7071 5.29289C10.8946 5.48043 11 5.73478 11 6V10Z"/>
                    <path d="M2 8H1C0.734784 8 0.48043 7.89464 0.292893 7.70711C0.105357 7.51957 0 7.26522 0 7V2C0 1.73478 0.105357 1.48043 0.292893 1.29289C0.48043 1.10536 0.734784 1 1 1H6C6.26522 1 6.51957 1.10536 6.70711 1.29289C6.89464 1.48043 7 1.73478 7 2V3H8V2C8 1.46957 7.78929 0.960859 7.41421 0.585786C7.03914 0.210714 6.53043 0 6 0H1C0.46957 0 0.0391 0.210714 0.585786 0.585786C0.210714 0.960859 0 1.46957 0 2V7C0 7.53043 0.210714 8.03914 0.585786 8.41421C0.960859 8.78929 1.46957 9 2 9V8Z"/>
                </svg>
            </button>
            <button
                type="button"
                class="node-button-icon node-button-icon-delete bg-white transition-colors"
                title="Eliminar"
                @click.stop="$emit('delete')"
            >
                <svg class="w-3 h-3" viewBox="0 0 12 14" fill="#ef4444">
                    <path d="M11 2H8.5L7.5 1H4.5L3.5 2H1V3H11V2Z"/>
                    <path d="M2 4V12C2 12.5304 2.21071 13.0391 2.58579 13.4142C2.96086 13.7893 3.46957 14 4 14H8C8.53043 14 9.03914 13.7893 9.41421 13.4142C9.78929 13.0391 10 12.5304 10 12V4H2ZM5 11H4V6H5V11ZM8 11H7V6H8V11Z"/>
                </svg>
            </button>
        </div>

        <!-- Node content -->
        <slot />
    </div>
</template>

<script setup>
import { ref } from 'vue';

defineEmits(['delete', 'duplicate']);

const isHovered = ref(false);
</script>

