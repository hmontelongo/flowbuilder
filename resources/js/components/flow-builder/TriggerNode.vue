<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <TriggerIcon />
        </template>

        <NodeDropdown
            v-model="selectedAction"
            placeholder="Selecciona una acción para iniciar el flujo"
            :sections="dropdownSections"
        />
    </BaseNode>
</template>

<script setup>
import { ref } from 'vue';
import BaseNode from './BaseNode.vue';
import NodeDropdown from './NodeDropdown.vue';
import { TriggerIcon } from './icons';

const selectedAction = ref('');

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

const nodeConfig = {
    header: {
        iconColor: '#34d1bf',
        icon: TriggerIcon,
    },
    card: {
        padding: '16px 8px',
        borderRadius: '8px 8px 4px 8px',
    },
    connectors: {
        input: true,
        output: true,
    },
};
</script>
