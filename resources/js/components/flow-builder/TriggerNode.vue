<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <TriggerIcon />
        </template>

        <!-- Trigger type dropdown -->
        <NodeDropdown
            v-model="selectedAction"
            placeholder="Selecciona una acción para iniciar el flujo"
            :sections="dropdownSections"
        />

        <!-- Keyword input (shown when keyword_message or regex is selected) -->
        <NodeInput
            v-if="showKeywordInput"
            v-model="keyword"
            :placeholder="keywordPlaceholder"
            icon="add"
            class="mt-2"
            @action="addKeyword"
        />

        <!-- Keywords list -->
        <div v-if="keywords.length > 0" class="mt-2 flex flex-wrap gap-1">
            <span
                v-for="kw in keywords"
                :key="kw"
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded node-text-xs"
                style="background-color: var(--color-fb-neutral-100);"
            >
                {{ kw }}
                <button
                    type="button"
                    class="hover:opacity-70"
                    @click.stop="removeKeyword(kw)"
                    @mousedown.stop
                >
                    <svg class="w-3 h-3" viewBox="0 0 10 10" fill="currentColor">
                        <path d="M1.5 1.5L8.5 8.5M8.5 1.5L1.5 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </button>
            </span>
        </div>

        <!-- Bottom row: Toggle + Help link (shown when keyword_message is selected) -->
        <div v-if="selectedAction === 'keyword_message'" class="mt-3 flex items-center justify-between">
            <NodeToggle
                v-model="exactMatch"
                label="Coincidencia exacta"
            />
            <a
                href="#"
                class="node-text-xs hover:underline"
                style="color: var(--color-fb-text-link);"
                @click.prevent="openHelp"
                @mousedown.stop
            >
                ¿Cómo funciona?
            </a>
        </div>
    </BaseNode>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useNode, useVueFlow } from '@vue-flow/core';
import BaseNode from './BaseNode.vue';
import { NodeDropdown, NodeInput, NodeToggle } from './shared';
import { TriggerIcon } from './icons';

// Vue Flow passes these props to custom nodes
const props = defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
});

// Vue Flow composables
const { id } = useNode();
const { updateNodeData } = useVueFlow();

// State - initialize from props.data (passed by Vue Flow)
const selectedAction = ref(props.data?.action || '');
const keyword = ref('');
const keywords = ref(props.data?.keywords || []);
const exactMatch = ref(props.data?.exactMatch || false);

// Watch for changes and update node data via Vue Flow
watch([selectedAction, keywords, exactMatch], () => {
    updateNodeData(id, {
        action: selectedAction.value,
        keywords: keywords.value,
        exactMatch: exactMatch.value,
    });
}, { deep: true });

// Dropdown sections for trigger types
const dropdownSections = [
    {
        label: 'General',
        options: [
            { value: 'keyword_message', label: 'Mensaje con palabra clave' },
            { value: 'any_message', label: 'Recibir cualquier mensaje' },
        ],
    },
    {
        label: 'Desde Sellia',
        options: [
            { value: 'survey', label: 'Encuesta' },
            { value: 'template', label: 'Template' },
            { value: 'whatsapp_flow', label: 'WhatsApp Flow' },
        ],
    },
    {
        label: 'Avanzado',
        options: [
            { value: 'regex', label: 'Expresión regular' },
        ],
    },
];

// Show keyword input for keyword_message and regex triggers
const showKeywordInput = computed(() => {
    return ['keyword_message', 'regex'].includes(selectedAction.value);
});

// Dynamic placeholder based on trigger type
const keywordPlaceholder = computed(() => {
    if (selectedAction.value === 'regex') {
        return 'Escribe una expresión regular';
    }
    return 'Escribe una palabra, por ej: hola';
});

// Node configuration for BaseNode
const nodeConfig = {
    header: {
        iconColor: 'var(--color-fb-node-trigger)',
        icon: TriggerIcon,
        tooltip: {
            title: 'Disparador',
            description: 'Define qué acción o evento iniciará este flujo. Puede ser un mensaje, palabra clave o evento externo.',
            linkText: 'Leer más',
            linkUrl: '#',
        },
    },
    connectors: {
        input: true,
        output: true,
    },
};

// Actions
const addKeyword = () => {
    const trimmed = keyword.value.trim();
    if (trimmed && !keywords.value.includes(trimmed)) {
        keywords.value.push(trimmed);
        keyword.value = '';
    }
};

const removeKeyword = (keywordToRemove) => {
    keywords.value = keywords.value.filter(kw => kw !== keywordToRemove);
};

const openHelp = () => {
    const el = document.getElementById('flow-canvas');
    if (el) {
        el.dispatchEvent(new CustomEvent('open-help', { detail: { topic: 'trigger-exact-match' } }));
    }
};
</script>
