<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <MessageIcon />
        </template>

        <!-- Initial state: Message type selector -->
        <div v-if="messages.length === 0" class="flex items-center justify-center gap-1">
            <button
                v-for="type in messageTypes"
                :key="type.id"
                type="button"
                class="message-type-btn flex items-center justify-center"
                :title="type.label"
                @click.stop="addMessage(type.id)"
                @mousedown.stop
            >
                <component :is="type.icon" />
            </button>
        </div>

        <!-- Messages list (after adding) -->
        <div v-else class="messages-container relative flex flex-col gap-1 w-full">
            <!-- Top add zone -->
            <div class="add-zone add-zone-top group/top">
                <div v-if="activeAddPosition === 'top'" class="type-picker">
                    <button
                        v-for="type in messageTypes"
                        :key="type.id"
                        type="button"
                        class="type-picker-btn"
                        :title="type.label"
                        @click.stop="insertMessageOfType(0, type.id)"
                        @mousedown.stop
                    >
                        <component :is="type.icon" />
                    </button>
                </div>
                <button
                    v-else
                    type="button"
                    class="add-btn-mini opacity-0 group-hover/top:opacity-100"
                    title="Agregar mensaje"
                    @click.stop="openTypePicker('top')"
                    @mousedown.stop
                >
                    <svg class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor">
                        <path d="M6.5 1H5.5V5.5H1V6.5H5.5V11H6.5V6.5H11V5.5H6.5V1Z"/>
                    </svg>
                </button>
            </div>

            <!-- Message items wrapped in consistent container -->
            <MessageItemContainer
                v-for="(message, index) in messages"
                :key="message.id"
                :is-dragging="draggedIndex === index"
                :is-drag-over="dragOverIndex === index"
                @remove="removeMessage(index)"
                @dragstart="handleDragStart(index)"
                @dragend="handleDragEnd"
                @dragover="handleDragOver(index)"
                @dragleave="handleDragLeave"
                @drop="handleDrop(index)"
            >
                <MessageContent :message="message" />
            </MessageItemContainer>

            <!-- Bottom add zone -->
            <div class="add-zone add-zone-bottom group/bottom">
                <div v-if="activeAddPosition === 'bottom'" class="type-picker">
                    <button
                        v-for="type in messageTypes"
                        :key="type.id"
                        type="button"
                        class="type-picker-btn"
                        :title="type.label"
                        @click.stop="addMessageOfType(type.id)"
                        @mousedown.stop
                    >
                        <component :is="type.icon" />
                    </button>
                </div>
                <button
                    v-else
                    type="button"
                    class="add-btn-mini opacity-0 group-hover/bottom:opacity-100"
                    title="Agregar mensaje"
                    @click.stop="openTypePicker('bottom')"
                    @mousedown.stop
                >
                    <svg class="w-3 h-3" viewBox="0 0 12 12" fill="currentColor">
                        <path d="M6.5 1H5.5V5.5H1V6.5H5.5V11H6.5V6.5H11V5.5H6.5V1Z"/>
                    </svg>
                </button>
            </div>
        </div>
    </BaseNode>
</template>

<script setup>
import { ref, markRaw, onMounted, onUnmounted } from 'vue';
import BaseNode from './BaseNode.vue';
import MessageContent from './MessageContent.vue';
import { MessageItemContainer } from './shared';
import { MessageIcon, TextMessageIcon, AttachmentIcon, ButtonMessageIcon, LinkMessageIcon, LocationMessageIcon, ListMessageIcon } from './icons';

// Message types available in WhatsApp Cloud API
const messageTypes = [
    { id: 'text', label: 'Texto', icon: markRaw(TextMessageIcon) },
    { id: 'attachment', label: 'Archivo adjunto', icon: markRaw(AttachmentIcon) },
    { id: 'button', label: 'Botones', icon: markRaw(ButtonMessageIcon) },
    { id: 'link', label: 'Enlace', icon: markRaw(LinkMessageIcon) },
    { id: 'location', label: 'Ubicación', icon: markRaw(LocationMessageIcon) },
    { id: 'list', label: 'Lista', icon: markRaw(ListMessageIcon) },
];

// Messages state
const messages = ref([]);

// Type picker state
const activeAddPosition = ref(null);

// Drag and drop state
const draggedIndex = ref(null);
const dragOverIndex = ref(null);

const openTypePicker = (position) => {
    activeAddPosition.value = position;
};

const closeTypePicker = () => {
    activeAddPosition.value = null;
};

const addMessage = (typeId) => {
    messages.value.push({
        id: `msg-${crypto.randomUUID()}`,
        type: typeId,
        content: '',
    });
};

const addMessageOfType = (typeId) => {
    addMessage(typeId);
    closeTypePicker();
};

const insertMessageOfType = (index, typeId) => {
    messages.value.splice(index, 0, {
        id: `msg-${crypto.randomUUID()}`,
        type: typeId,
        content: '',
    });
    closeTypePicker();
};

const removeMessage = (index) => {
    messages.value.splice(index, 1);
};

// Drag and drop handlers
const handleDragStart = (index) => {
    draggedIndex.value = index;
};

const handleDragEnd = () => {
    draggedIndex.value = null;
    dragOverIndex.value = null;
};

const handleDragOver = (index) => {
    if (draggedIndex.value !== null && draggedIndex.value !== index) {
        dragOverIndex.value = index;
    }
};

const handleDragLeave = () => {
    dragOverIndex.value = null;
};

const handleDrop = (dropIndex) => {
    if (draggedIndex.value !== null && draggedIndex.value !== dropIndex) {
        const draggedMessage = messages.value.splice(draggedIndex.value, 1)[0];
        messages.value.splice(dropIndex, 0, draggedMessage);
    }
    draggedIndex.value = null;
    dragOverIndex.value = null;
};

// Close type picker when clicking outside
const handleClickOutside = () => {
    if (activeAddPosition.value) {
        closeTypePicker();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const nodeConfig = {
    header: {
        iconColor: 'var(--color-fb-node-message)',
        icon: MessageIcon,
        tooltip: {
            title: 'Mensaje',
            description: 'Envía un mensaje de texto, imagen, video, documento u otro tipo de contenido al usuario.',
            linkText: 'Leer más',
            linkUrl: '#',
        },
    },
    connectors: {
        input: true,
        output: true,
    },
};
</script>

<style scoped>
.messages-container {
    overflow: visible;
}

.message-type-btn {
    width: 24px;
    height: 24px;
    border: 1px solid var(--color-fb-neutral-500);
    border-radius: 4px;
    color: var(--color-fb-neutral-600);
    background: transparent;
    cursor: pointer;
    transition: all 0.15s ease;
}

.message-type-btn:hover {
    background: var(--color-fb-neutral-100);
    border-color: var(--color-fb-neutral-700);
    color: var(--color-fb-neutral-800);
}

.add-zone {
    position: absolute;
    left: 0;
    right: 0;
    height: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.add-zone-top {
    top: -6px;
}

.add-zone-bottom {
    bottom: -6px;
}

.add-btn-mini {
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--color-fb-neutral-500);
    border-radius: 3px;
    background: var(--color-fb-neutral-0);
    color: var(--color-fb-neutral-500);
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    transition: opacity 0.15s ease, background-color 0.15s ease, box-shadow 0.15s ease;
}

.add-btn-mini:hover {
    background: var(--color-fb-neutral-100);
    border-color: var(--color-fb-neutral-600);
    color: var(--color-fb-neutral-700);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
}

.type-picker {
    display: flex;
    align-items: center;
    gap: 2px;
    padding: 2px;
    background: var(--color-fb-neutral-0);
    border: 1px solid var(--color-fb-neutral-300);
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.type-picker-btn {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 3px;
    color: var(--color-fb-neutral-600);
    background: transparent;
    cursor: pointer;
    transition: all 0.15s ease;
}

.type-picker-btn:hover {
    background: var(--color-fb-neutral-100);
    color: var(--color-fb-neutral-800);
}
</style>
