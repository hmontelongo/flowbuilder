<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <MessageIcon />
        </template>

        <div class="flex flex-col items-center gap-2 w-full">
            <!-- Add message button -->
            <button type="button" class="node-button-add">
                <svg class="w-4 h-4" viewBox="0 0 8 8" fill="var(--color-fb-text)">
                    <path d="M4.5 3.5V0H3.5V3.5H0V4.5H3.5V8H4.5V4.5H8V3.5H4.5Z"/>
                </svg>
            </button>

            <!-- Message input area -->
            <div class="node-bg-input flex flex-col items-end gap-2" style="width: 277px;">
                <!-- Toolbar -->
                <div class="flex items-center justify-between w-full">
                    <svg class="w-4 h-4" viewBox="0 0 16 16" fill="var(--color-fb-text)">
                        <rect x="1" y="3" width="14" height="2"/>
                        <rect x="1" y="7" width="10" height="2"/>
                        <rect x="1" y="11" width="7" height="2"/>
                    </svg>
                    <button type="button" class="p-1 hover:bg-gray-200 rounded">
                        <svg class="w-4 h-4" viewBox="0 0 16 16" fill="var(--color-fb-text)">
                            <path d="M12 4L4 12M4 4L12 12" stroke="var(--color-fb-text)" stroke-width="1.5" fill="none"/>
                        </svg>
                    </button>
                </div>

                <!-- Input with file icons -->
                <div class="node-input flex flex-col items-center gap-2 w-full" style="border-radius: 2px;">
                    <div class="flex items-center gap-1">
                        <!-- File type icons -->
                        <button type="button" class="p-0.5 hover:bg-gray-100 rounded" title="Imagen">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="var(--color-fb-text-muted)">
                                <rect x="2" y="2" width="12" height="12" rx="1" stroke="var(--color-fb-text-muted)" fill="none"/>
                                <circle cx="5" cy="5" r="1"/>
                                <path d="M2 12L6 8L9 11L11 9L14 12"/>
                            </svg>
                        </button>
                        <button type="button" class="p-0.5 hover:bg-gray-100 rounded" title="Video">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="var(--color-fb-text-muted)">
                                <rect x="1" y="3" width="10" height="10" rx="1" stroke="var(--color-fb-text-muted)" fill="none"/>
                                <path d="M11 6L15 4V12L11 10"/>
                            </svg>
                        </button>
                        <button type="button" class="p-0.5 hover:bg-gray-100 rounded" title="Documento">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="var(--color-fb-text-muted)">
                                <path d="M4 1H10L13 4V14C13 14.5 12.5 15 12 15H4C3.5 15 3 14.5 3 14V2C3 1.5 3.5 1 4 1Z" stroke="var(--color-fb-text-muted)" fill="none"/>
                                <path d="M10 1V4H13"/>
                            </svg>
                        </button>
                        <button type="button" class="p-0.5 hover:bg-gray-100 rounded" title="Sticker">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="var(--color-fb-text-muted)">
                                <rect x="2" y="2" width="12" height="12" rx="2" stroke="var(--color-fb-text-muted)" fill="none"/>
                                <path d="M10 2V6C10 7 11 8 12 8H14"/>
                            </svg>
                        </button>
                        <button type="button" class="p-0.5 hover:bg-gray-100 rounded" title="Audio">
                            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="var(--color-fb-text-muted)">
                                <circle cx="4" cy="12" r="2" stroke="var(--color-fb-text-muted)" fill="none"/>
                                <circle cx="12" cy="10" r="2" stroke="var(--color-fb-text-muted)" fill="none"/>
                                <path d="M6 12V4L14 2V10"/>
                            </svg>
                        </button>
                    </div>
                    <span class="node-text-xs node-text-placeholder text-center w-full overflow-hidden text-ellipsis">Selecciona o arrastra el archivo a adjuntar</span>
                </div>

                <!-- Formats link -->
                <span class="node-text-link">Formatos recomendados</span>
            </div>

            <!-- Add message button (bottom) -->
            <button type="button" class="node-button-add">
                <svg class="w-4 h-4" viewBox="0 0 8 8" fill="var(--color-fb-text)">
                    <path d="M4.5 3.5V0H3.5V3.5H0V4.5H3.5V8H4.5V4.5H8V3.5H4.5Z"/>
                </svg>
            </button>
        </div>
    </BaseNode>
</template>

<script setup>
import BaseNode from './BaseNode.vue';
import { MessageIcon } from './icons';

const nodeConfig = {
    header: {
        iconColor: '#3454d1',
        icon: MessageIcon,
    },
    card: {
        padding: '8px',
        borderRadius: '8px',
    },
    connectors: {
        input: true,
        output: true,
    },
};
</script>
