<template>
    <div class="relative inline-block" @mouseenter="show = true" @mouseleave="show = false">
        <slot />

        <!-- Tooltip positioned ABOVE with arrow pointing down -->
        <Transition name="tooltip">
            <div
                v-if="show && (title || description)"
                class="absolute z-50 flex flex-col"
                :style="positionStyles"
            >
                <!-- Tooltip content -->
                <div
                    class="rounded px-2 py-1 flex flex-col gap-2"
                    :style="{
                        width: '240px',
                        backgroundColor: 'var(--color-fb-neutral-200)',
                    }"
                >
                    <!-- Title -->
                    <p v-if="title" class="text-xs leading-4" :style="{ color: 'var(--color-fb-neutral-600)' }">
                        <span class="font-medium">{{ title }}</span>
                    </p>

                    <!-- Description -->
                    <p v-if="description" class="text-[10px] leading-[14px]" :style="{ color: 'var(--color-fb-neutral-600)' }">
                        {{ description }}
                    </p>

                    <!-- Link (right-aligned) -->
                    <div v-if="linkText && linkUrl" class="flex justify-end">
                        <a
                            :href="linkUrl"
                            class="text-[10px] leading-[14px] underline"
                            :style="{ color: 'var(--color-fb-text-link)' }"
                            target="_blank"
                            @click.stop
                        >
                            {{ linkText }}
                        </a>
                    </div>
                </div>

                <!-- Arrow pointing down, centered with icon (12px from left) -->
                <svg class="arrow-svg" width="28" height="6" viewBox="0 0 28 6">
                    <path d="M14 6L7 0H21L14 6Z" fill="#e5e7eb"/>
                </svg>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    linkText: {
        type: String,
        default: '',
    },
    linkUrl: {
        type: String,
        default: '',
    },
});

const show = ref(false);

// Position tooltip above the trigger, aligned to start
const positionStyles = computed(() => ({
    bottom: '100%',
    left: '0',
    marginBottom: '2px',
}));
</script>

<style scoped>
.tooltip-enter-active,
.tooltip-leave-active {
    transition: opacity 0.15s ease;
}

.tooltip-enter-from,
.tooltip-leave-to {
    opacity: 0;
}

/* Center arrow with icon (icon is 24px, center at 12px) */
.arrow-svg {
    margin-left: -2px; /* 12px (icon center) - 14px (arrow center) = -2px */
}
</style>
