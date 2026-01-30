<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <WaitIcon />
        </template>

        <template #extra-connectors="{ isHandleConnected }">
            <!-- No response output connector (red border) -->
            <PositionedConnector
                id="no-response"
                type="source"
                side="right"
                variant="error"
                :connected="isHandleConnected('no-response').value"
                :top="noResponseConnectorTop"
            />
        </template>

        <div ref="contentRef" class="flex flex-col items-end gap-4 w-full">
            <!-- Timer section -->
            <div class="flex flex-col gap-2 w-full">
                <div class="flex items-center gap-2">
                    <span class="node-text-label">Esperar respuesta por:</span>
                    <div class="node-input flex items-center gap-1" style="padding: 4px 8px;">
                        <!-- Timer icon -->
                        <svg class="w-4 h-4" viewBox="0 0 11.4402 12.9707" fill="var(--color-fb-icon)">
                            <path d="M5.94016 4.5H4.94016V9H5.94016V4.5Z"/>
                            <path d="M6.94016 0H3.94016V1H6.94016V0Z"/>
                            <path d="M11.4402 3.5L10.7302 2.795L9.60515 3.92C8.68426 2.85652 7.38903 2.18872 5.98857 2.05535C4.58812 1.92199 3.19013 2.33332 2.08505 3.20387C0.979978 4.07443 0.252792 5.33727 0.0545839 6.73003C-0.143624 8.12278 0.202385 9.53836 1.02072 10.6826C1.83905 11.8269 3.0668 12.6119 4.44885 12.8746C5.83091 13.1372 7.26102 12.8572 8.44204 12.0929C9.62307 11.3285 10.4642 10.1385 10.7907 8.77016C11.1172 7.40178 10.9039 5.96022 10.1952 4.745L11.4402 3.5ZM5.44016 12C4.55014 12 3.68011 11.7361 2.94009 11.2416C2.20007 10.7471 1.62329 10.0443 1.2827 9.22208C0.942103 8.39981 0.852988 7.49501 1.02662 6.62209C1.20026 5.74918 1.62884 4.94736 2.25817 4.31802C2.88751 3.68868 3.68933 3.2601 4.56225 3.08647C5.43516 2.91283 6.33996 3.00195 7.16223 3.34254C7.9845 3.68314 8.6873 4.25991 9.18177 4.99993C9.67623 5.73995 9.94016 6.60998 9.94016 7.5C9.94016 8.69347 9.46605 9.83807 8.62214 10.682C7.77822 11.5259 6.63363 12 5.44016 12Z"/>
                        </svg>
                        <div class="flex items-center gap-1">
                            <span class="node-text-muted">{{ waitTime }}</span>
                            <!-- Chevron sort icon -->
                            <svg class="w-3 h-3" viewBox="0 0 7 12" fill="var(--color-fb-icon)">
                                <path d="M3.5 12L0 8.5L0.705 7.795L3.5 10.585L6.295 7.795L7 8.5L3.5 12Z"/>
                                <path d="M3.5 0L7 3.5L6.295 4.205L3.5 1.415L0.705 4.205L0 3.5L3.5 0Z"/>
                            </svg>
                            <span class="node-text-muted">{{ timeUnit }}</span>
                            <!-- Chevron sort icon -->
                            <svg class="w-3 h-3" viewBox="0 0 7 12" fill="var(--color-fb-icon)">
                                <path d="M3.5 12L0 8.5L0.705 7.795L3.5 10.585L6.295 7.795L7 8.5L3.5 12Z"/>
                                <path d="M3.5 0L7 3.5L6.295 4.205L3.5 1.415L0.705 4.205L0 3.5L3.5 0Z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Fallback message input -->
                <div class="node-input-message flex flex-col items-end w-full">
                    <div class="flex flex-col gap-0.5 w-full">
                        <span class="node-text-label w-full" style="min-height: 20px; padding: 2px;">{{ fallbackMessage || 'Mensaje a enviar si no recibes respuesta a tiempo' }}</span>
                    </div>
                    <span class="node-text-label text-right">4,096</span>
                </div>
            </div>

            <!-- Add action button -->
            <div class="flex items-center justify-center w-full">
                <button type="button" class="node-button-add-sm">
                    <svg class="w-4 h-4" viewBox="0 0 8 8" fill="var(--color-fb-text)">
                        <path d="M4.5 3.5V0H3.5V3.5H0V4.5H3.5V8H4.5V4.5H8V3.5H4.5Z"/>
                    </svg>
                </button>
            </div>

            <!-- No response label -->
            <span ref="noResponseLabelRef" class="node-text-xs node-text-placeholder w-full text-right uppercase">SI NO SE RECIBE RESPUESTA</span>
        </div>
    </BaseNode>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import BaseNode from './BaseNode.vue';
import { PositionedConnector } from './shared';
import { WaitIcon } from './icons';

const waitTime = ref(10);
const timeUnit = ref('Segundos');
const fallbackMessage = ref('');

// Refs for DOM measurement
const contentRef = ref(null);
const noResponseLabelRef = ref(null);
const noResponseConnectorTop = ref(50); // Default fallback

// ResizeObserver for layout changes
let resizeObserver = null;
let isUnmounted = false;

const recalculateConnectorPosition = () => {
    if (isUnmounted || !contentRef.value || !noResponseLabelRef.value) return;

    const cardWrapper = contentRef.value.closest('.node-card-wrapper');
    if (!cardWrapper) return;

    // Calculate position using offsetTop chain
    let top = noResponseLabelRef.value.offsetTop + (noResponseLabelRef.value.offsetHeight / 2);
    let el = noResponseLabelRef.value.offsetParent;

    while (el && el !== cardWrapper && !el.classList?.contains('node-card-wrapper')) {
        top += el.offsetTop;
        el = el.offsetParent;
    }

    noResponseConnectorTop.value = top;
};

// Setup ResizeObserver when content is available
watch(contentRef, (container) => {
    if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }
    if (container && !isUnmounted) {
        resizeObserver = new ResizeObserver(() => {
            if (!isUnmounted) {
                recalculateConnectorPosition();
            }
        });
        resizeObserver.observe(container);
        // Initial calculation after mount
        setTimeout(recalculateConnectorPosition, 10);
    }
}, { immediate: true });

onUnmounted(() => {
    isUnmounted = true;
    if (resizeObserver) {
        resizeObserver.disconnect();
        resizeObserver = null;
    }
});

const nodeConfig = {
    header: {
        iconColor: 'var(--color-fb-node-wait)',
        icon: WaitIcon,
        tooltip: {
            title: 'Espera',
            description: 'Pausa el flujo hasta recibir una respuesta del usuario o hasta que expire el tiempo definido.',
            linkText: 'Leer más',
            linkUrl: '#',
        },
    },
    connectors: {
        input: true,
        output: { id: 'output' },
    },
};
</script>
