<template>
    <div ref="triggerRef" class="relative inline-block" @click.stop="toggleTooltip">
        <slot />

        <!-- Tooltip positioned dynamically based on viewport boundaries -->
        <Transition name="tooltip">
            <div
                v-if="show && (title || description)"
                ref="tooltipRef"
                class="absolute z-50 flex"
                :class="tooltipContainerClass"
                :style="positionStyles"
                @click.stop
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

                <!-- Arrow pointing down (when tooltip above) -->
                <svg v-if="placement === 'top'" class="arrow-svg arrow-down" width="28" height="6" viewBox="0 0 28 6">
                    <path d="M14 6L7 0H21L14 6Z" fill="#e5e7eb"/>
                </svg>
                <!-- Arrow pointing up (when tooltip below) -->
                <svg v-else class="arrow-svg arrow-up" width="28" height="6" viewBox="0 0 28 6">
                    <path d="M14 0L21 6H7L14 0Z" fill="#e5e7eb"/>
                </svg>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';

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
const triggerRef = ref(null);
const tooltipRef = ref(null);
const placement = ref('top'); // 'top' or 'bottom'
const horizontalOffset = ref(0); // Offset to keep tooltip in viewport

const TOOLTIP_WIDTH = 240;
const TOOLTIP_HEIGHT_ESTIMATE = 100; // Approximate, will be measured
const VIEWPORT_MARGIN = 8;

const toggleTooltip = () => {
    if (show.value) {
        show.value = false;
    } else {
        show.value = true;
        nextTick(updatePlacement);
    }
};

const updatePlacement = () => {
    if (!triggerRef.value) return;

    const triggerRect = triggerRef.value.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    // Check vertical placement
    const spaceAbove = triggerRect.top;
    const spaceBelow = viewportHeight - triggerRect.bottom;

    // Prefer above, but use below if not enough space
    if (spaceAbove < TOOLTIP_HEIGHT_ESTIMATE + VIEWPORT_MARGIN && spaceBelow > spaceAbove) {
        placement.value = 'bottom';
    } else {
        placement.value = 'top';
    }

    // Check horizontal overflow
    const tooltipLeft = triggerRect.left;
    const tooltipRight = tooltipLeft + TOOLTIP_WIDTH;

    if (tooltipRight > viewportWidth - VIEWPORT_MARGIN) {
        // Would overflow right - shift left
        horizontalOffset.value = viewportWidth - VIEWPORT_MARGIN - tooltipRight;
    } else if (tooltipLeft < VIEWPORT_MARGIN) {
        // Would overflow left - shift right
        horizontalOffset.value = VIEWPORT_MARGIN - tooltipLeft;
    } else {
        horizontalOffset.value = 0;
    }
};

// Position styles based on calculated placement
const positionStyles = computed(() => {
    const styles = {
        left: `${horizontalOffset.value}px`,
    };

    if (placement.value === 'top') {
        styles.bottom = '100%';
        styles.marginBottom = '2px';
    } else {
        styles.top = '100%';
        styles.marginTop = '2px';
    }

    return styles;
});

// Container class for flex direction based on placement
const tooltipContainerClass = computed(() => ({
    'flex-col': placement.value === 'top',
    'flex-col-reverse': placement.value === 'bottom',
}));

// Close tooltip when clicking outside
const handleClickOutside = () => {
    if (show.value) {
        show.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
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
    flex-shrink: 0;
}

.arrow-down {
    order: 2;
}

.arrow-up {
    order: -1;
}
</style>
