<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <CanalIcon />
        </template>

        <!-- Show dropdown when editing OR no channel selected -->
        <ChannelDropdown
            v-if="showDropdown"
            v-model="selectedChannelId"
            :channels="availableChannels"
            :placeholder="placeholder"
            :auto-open="isEditing"
            @open="handleDropdownOpen"
            @close="handleDropdownClose"
            @add-channel="handleAddChannel"
        />

        <!-- Show selected channel display when channel is chosen and not editing -->
        <SelectedChannelDisplay
            v-else-if="selectedChannel"
            :channel="selectedChannel"
            :readonly="isReadonly"
            @edit="isEditing = true"
        />

        <!-- Error message when in error state -->
        <div
            v-if="nodeState.mode === 'error'"
            class="mt-2 node-text-xs node-text-warning"
        >
            {{ nodeState.errorMessage || 'Selecciona un canal para continuar' }}
        </div>
    </BaseNode>
</template>

<script setup>
import { ref, computed, inject, watch } from 'vue';
import { useNode } from '@vue-flow/core';
import BaseNode from './BaseNode.vue';
import { ChannelDropdown } from './shared';
import SelectedChannelDisplay from './SelectedChannelDisplay.vue';
import { CanalIcon } from './icons';

// Vue Flow's useNode provides id, node object
const { id, node } = useNode();

// Inject available channels from FlowCanvas
const availableChannels = inject('availableChannels', ref([
    // Default mock data for development
    { id: 'ch-1', type: 'whatsapp', name: 'Canal Principal', phone: '+52 123 456 7890' },
    { id: 'ch-2', type: 'whatsapp', name: 'Canal Ventas', phone: '+52 123 456 7891' },
    { id: 'ch-3', type: 'webchat', name: 'Web Chat', phone: null },
]));

// Local state
const isEditing = ref(false);
const selectedChannelId = ref(node.value?.data?.channelId || '');

// Computed state from node data
const nodeState = computed(() => node.value?.data?.state ?? { mode: 'normal' });
const isReadonly = computed(() => nodeState.value.mode === 'readonly');

// Find selected channel object
const selectedChannel = computed(() => {
    if (!selectedChannelId.value) return null;
    const channels = Array.isArray(availableChannels.value) ? availableChannels.value : availableChannels;
    return channels.find(ch => ch.id === selectedChannelId.value) || null;
});

// Show dropdown when editing, in error state without selection, or no channel selected
const showDropdown = computed(() => {
    if (isReadonly.value && selectedChannel.value) return false;
    if (isEditing.value) return true;
    if (!selectedChannel.value) return true;
    return false;
});

const placeholder = computed(() => {
    if (nodeState.value.mode === 'error') {
        return 'Selecciona un canal';
    }
    return 'Buscar un canal';
});

// Node config for BaseNode
const nodeConfig = computed(() => ({
    header: {
        iconColor: 'var(--color-fb-node-canal)',
        icon: CanalIcon,
        tooltip: {
            title: 'Canal',
            description: 'Define dónde se mostrará este flujo y cómo funcionarán los mensajes.',
            linkText: 'Leer más',
            linkUrl: '#',
        },
    },
    connectors: {
        input: false,
        output: true,
    },
}));

// Event handlers
const handleDropdownOpen = () => {
    isEditing.value = true;
};

const handleDropdownClose = () => {
    isEditing.value = false;
};

const handleAddChannel = (channelType) => {
    // Emit event for parent to handle (e.g., open modal to create new channel)
    emitDomEvent('add-channel-request', { nodeId: id, channelType });
};

// Emit DOM events for Alpine/Livewire bridge
const emitDomEvent = (name, detail) => {
    const el = document.getElementById('flow-canvas');
    if (el) {
        el.dispatchEvent(new CustomEvent(name, { detail }));
    }
};

// Watch for channel selection changes and emit to Livewire
// Note: handleDropdownClose() is the ONLY place that sets isEditing = false
watch(selectedChannelId, (newValue, oldValue) => {
    // Only emit when selection actually changes
    if (newValue && newValue !== oldValue) {
        emitDomEvent('node-data-updated', {
            nodeId: id,
            data: { channelId: newValue },
        });
    }
});
</script>
