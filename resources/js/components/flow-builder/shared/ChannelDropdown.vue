<template>
    <div ref="triggerRef" class="relative">
        <!-- Search input -->
        <div
            class="node-input flex items-center gap-2"
            :class="{ 'node-input-focus': isOpen }"
            @click.stop="openDropdown"
            @mousedown.stop
        >
            <input
                ref="searchInput"
                v-model="searchQuery"
                type="text"
                :placeholder="placeholder"
                class="node-text-xs flex-1 outline-none bg-transparent min-w-0"
                @focus="openDropdown"
                @input="openDropdown"
            />
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 14 14" :style="{ fill: 'var(--color-fb-neutral-700)' }">
                <path d="M13.5177 12.8106L9.74165 9.0346C10.649 7.94528 11.1015 6.54808 11.0049 5.13366C10.9084 3.71923 10.2702 2.39649 9.22323 1.44058C8.17625 0.484678 6.80105 -0.0307847 5.3837 0.00142333C3.96635 0.0336313 2.61598 0.61103 1.61351 1.61351C0.61103 2.61598 0.0336313 3.96635 0.00142333 5.3837C-0.0307847 6.80105 0.484678 8.17625 1.44058 9.22323C2.39649 10.2702 3.71923 10.9084 5.13366 11.0049C6.54808 11.1015 7.94528 10.649 9.0346 9.74165L12.8106 13.5177L13.5177 12.8106ZM1.0177 5.5177C1.0177 4.62768 1.28162 3.75765 1.77608 3.01763C2.27055 2.27761 2.97335 1.70083 3.79562 1.36024C4.61789 1.01964 5.52269 0.930529 6.3956 1.10416C7.26852 1.2778 8.07034 1.70638 8.69968 2.33572C9.32901 2.96505 9.7576 3.76688 9.93123 4.63979C10.1049 5.5127 10.0157 6.4175 9.67515 7.23977C9.33456 8.06204 8.75778 8.76484 8.01776 9.25931C7.27774 9.75378 6.40771 10.0177 5.5177 10.0177C4.32463 10.0164 3.1808 9.54184 2.33718 8.69822C1.49355 7.85459 1.01902 6.71076 1.0177 5.5177Z"/>
            </svg>
        </div>

        <!-- Dropdown menu (teleported to body for proper z-index) -->
        <Teleport to="body">
            <div
                v-if="isOpen && groupedChannels.length > 0"
                class="fixed flex flex-col gap-4 rounded-lg pb-4 pt-2 px-2"
                :style="dropdownStyle"
                @click.stop
                @mousedown.stop
            >
            <!-- Channel sections -->
            <template v-for="(group, index) in groupedChannels" :key="group.type">
                <div class="flex flex-col gap-1">
                    <!-- Section header with icon - Figma: gap-8px h-22px items-center p-2px rounded-4px -->
                    <div class="flex gap-2 h-[22px] items-center p-0.5 rounded">
                        <component
                            :is="getChannelIcon(group.type)"
                            class="w-4 h-4 shrink-0"
                        />
                        <span class="flex-1 text-xs font-medium leading-4" :style="{ color: 'var(--color-fb-neutral-800)' }">{{ group.label }}</span>
                    </div>

                    <!-- Channel items - Figma: px-8px py-2px rounded-2px -->
                    <div
                        v-for="channel in group.channels"
                        :key="channel.id"
                        class="channel-item flex flex-col px-2 py-0.5 rounded-sm cursor-pointer"
                        :class="{ 'is-selected': modelValue === channel.id }"
                        @click="selectChannel(channel)"
                    >
                        <!-- Name - Figma: 12px regular #404040 leading-14px -->
                        <span class="text-xs font-normal leading-[14px]" :style="{ color: 'var(--color-fb-neutral-600)' }">{{ channel.name }}</span>
                        <!-- Phone/URL - Figma: 10px regular #7e7e7e -->
                        <span v-if="channel.phone" class="text-[10px] font-normal leading-normal" :style="{ color: 'var(--color-fb-neutral-500)' }">{{ channel.phone }}</span>
                    </div>
                </div>
            </template>

            <!-- Divider - Figma: bg-#d1d5dc h-1px rounded-4px -->
            <div class="h-px rounded w-full" :style="{ backgroundColor: 'var(--color-fb-neutral-300)' }"></div>

            <!-- Nuevo canal button - Figma: same style as section header, NOT dashed -->
            <div
                class="nuevo-canal-btn flex gap-2 items-center p-0.5 rounded cursor-pointer"
                @click.stop="$emit('add-channel')"
            >
                <PlusIcon class="w-4 h-4 shrink-0" :style="{ color: 'var(--color-fb-neutral-800)' }" />
                <span class="flex-1 text-xs font-medium leading-4" :style="{ color: 'var(--color-fb-neutral-800)' }">Nuevo canal</span>
            </div>
        </div>

            <!-- Empty state when no channels match search -->
            <div
                v-if="isOpen && groupedChannels.length === 0"
                class="fixed flex items-center justify-center py-4 rounded-lg"
                :style="dropdownStyle"
                @click.stop
                @mousedown.stop
            >
                <span class="text-xs" :style="{ color: 'var(--color-fb-neutral-500)' }">No se encontraron canales</span>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { WhatsAppIcon, WebchatIcon, PlusIcon } from '../icons';

// Flag to ignore click events during the same tick as opening
let ignoreNextClick = false;

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    channels: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Buscar un canal',
    },
    autoOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'add-channel', 'open', 'close']);

const triggerRef = ref(null);
const isOpen = ref(false);
const searchQuery = ref('');
const searchInput = ref(null);
const dropdownPosition = ref({ top: 0, left: 0, width: 0, maxHeight: 280 });

const dropdownStyle = computed(() => ({
    top: `${dropdownPosition.value.top}px`,
    left: `${dropdownPosition.value.left}px`,
    width: `${dropdownPosition.value.width}px`,
    zIndex: 99999,
    maxHeight: `${dropdownPosition.value.maxHeight}px`,
    overflowY: 'auto',
    backgroundColor: 'var(--color-fb-neutral-50)',
    boxShadow: 'var(--fb-shadow-dropdown)',
}));

const updatePosition = () => {
    if (triggerRef.value) {
        const rect = triggerRef.value.getBoundingClientRect();
        const top = rect.bottom + 4;
        // Calculate available space from dropdown top to viewport bottom (with 16px margin)
        const viewportHeight = window.innerHeight;
        const availableHeight = viewportHeight - top - 16;
        // Use smaller of desired max (280) or available space, with minimum of 100
        const maxHeight = Math.max(100, Math.min(280, availableHeight));

        dropdownPosition.value = {
            top,
            left: rect.left,
            width: rect.width,
            maxHeight,
        };
    }
};

const channelTypeLabels = {
    whatsapp: 'WhatsApp',
    webchat: 'Webchat',
};

const filteredChannels = computed(() => {
    if (!searchQuery.value) return props.channels;
    const query = searchQuery.value.toLowerCase();
    return props.channels.filter(channel =>
        channel.name.toLowerCase().includes(query) ||
        (channel.phone && channel.phone.includes(query))
    );
});

const groupedChannels = computed(() => {
    const groups = {};
    filteredChannels.value.forEach(channel => {
        if (!groups[channel.type]) {
            groups[channel.type] = {
                type: channel.type,
                label: channelTypeLabels[channel.type] || channel.type,
                channels: [],
            };
        }
        groups[channel.type].channels.push(channel);
    });
    return Object.values(groups);
});

const getChannelIcon = (type) => {
    switch (type) {
        case 'whatsapp': return WhatsAppIcon;
        case 'webchat': return WebchatIcon;
        default: return WebchatIcon;
    }
};

const openDropdown = () => {
    if (!isOpen.value) {
        updatePosition();
        isOpen.value = true;
        // Ignore any click events that fire during this event cycle
        ignoreNextClick = true;
        setTimeout(() => { ignoreNextClick = false; }, 0);
        emit('open');
        nextTick(() => {
            searchInput.value?.focus();
        });
    }
};

const closeDropdown = () => {
    if (isOpen.value) {
        isOpen.value = false;
        searchQuery.value = '';
        emit('close');
    }
};

const selectChannel = (channel) => {
    emit('update:modelValue', channel.id);
    closeDropdown();
};

const handleClickOutside = (event) => {
    // Ignore clicks that happen in the same event cycle as opening
    // (prevents race condition when clicking SelectedChannelDisplay to edit)
    if (isOpen.value && !ignoreNextClick) {
        closeDropdown();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);

    // Auto-open dropdown when mounted with autoOpen prop (e.g., when editing existing selection)
    if (props.autoOpen) {
        nextTick(() => {
            openDropdown();
        });
    }
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
input::placeholder {
    color: var(--color-fb-input-placeholder);
}

.channel-item:hover {
    background-color: var(--color-fb-neutral-100);
}

.channel-item.is-selected {
    background-color: var(--color-fb-neutral-200);
}

.nuevo-canal-btn:hover {
    background-color: var(--color-fb-neutral-100);
}
</style>
