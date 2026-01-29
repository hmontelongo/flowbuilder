<template>
    <!-- Figma: No border/input box - just flex gap-4px items-center -->
    <div
        class="flex flex-col gap-0.5 w-full"
        :class="{ 'cursor-pointer': !readonly }"
        @click.stop="handleClick"
        @mousedown.stop
    >
        <!-- Row 1: Icon + Name - Figma: gap-4px items-center -->
        <div class="flex gap-1 items-center w-full">
            <!-- Channel type icon 16x16 -->
            <component
                :is="channelIcon"
                class="w-4 h-4 shrink-0"
            />
            <!-- Channel name - Figma: 12px medium #364153 leading-16px, truncate -->
            <span class="flex-1 text-xs font-medium leading-4 truncate" :style="{ color: 'var(--color-fb-neutral-700)' }">{{ channel.name }}</span>
        </div>

        <!-- Row 2: Phone/URL - Figma: 10px regular #7e7e7e leading-14px -->
        <div v-if="channel.phone" class="flex items-center w-full">
            <span class="flex-1 text-[10px] font-normal leading-[14px] truncate" :style="{ color: 'var(--color-fb-neutral-500)' }">{{ channel.phone }}</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { WhatsAppIcon, WebchatIcon } from './icons';

const props = defineProps({
    channel: {
        type: Object,
        required: true,
    },
    readonly: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['edit']);

const channelIcon = computed(() => {
    switch (props.channel.type) {
        case 'whatsapp': return WhatsAppIcon;
        case 'webchat': return WebchatIcon;
        default: return WebchatIcon;
    }
});

const handleClick = () => {
    if (!props.readonly) {
        emit('edit');
    }
};
</script>
