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
import { NodeDropdown } from './shared';
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
</script>
