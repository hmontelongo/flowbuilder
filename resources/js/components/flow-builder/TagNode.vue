<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <TagIcon />
        </template>

        <div class="flex flex-col gap-1 w-full">
            <!-- Tag name input -->
            <NodeInput
                v-model="tagName"
                placeholder="Asigna un nombre, ej: respuesta_menu"
            />

            <!-- Scope dropdown -->
            <NodeDropdown
                v-model="selectedScope"
                placeholder="Selecciona el alcance"
                :sections="scopeSections"
            />
        </div>
    </BaseNode>
</template>

<script setup>
import { ref } from 'vue';
import BaseNode from './BaseNode.vue';
import { NodeInput, NodeDropdown } from './shared';
import { TagIcon } from './icons';

const tagName = ref('');
const selectedScope = ref('this_chatbot');

const scopeSections = [
    {
        label: 'Alcance',
        options: [
            { value: 'this_chatbot', label: 'Usar solo en este chatbot' },
            { value: 'all_chatbots', label: 'Usar en todos los chatbots' },
        ],
    },
];

const nodeConfig = {
    header: {
        iconColor: 'var(--color-fb-node-tag)',
        icon: TagIcon,
        tooltip: {
            title: 'Etiqueta',
            description: 'Asigna una etiqueta a la conversación para clasificarla y facilitar su seguimiento posterior.',
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
