<template>
    <BaseNode :config="nodeConfig">
        <template #icon>
            <TagIcon />
        </template>

        <div class="flex flex-col gap-1 w-full">
            <!-- Tag name input -->
            <div class="node-input flex items-center w-full">
                <input
                    v-model="tagName"
                    type="text"
                    placeholder="Asigna un nombre, ej: respuesta_menu"
                    class="node-text-xs flex-1 outline-none bg-transparent min-w-0"
                    @click.stop
                    @mousedown.stop
                />
            </div>

            <!-- Scope dropdown -->
            <div
                class="node-input node-input-dark flex items-center justify-between w-full cursor-pointer"
                @click.stop="toggleScopeDropdown"
                @mousedown.stop
            >
                <span class="node-text-xs" style="color: var(--color-fb-text-dark);">{{ selectedScope }}</span>
                <svg class="w-4 h-4" viewBox="0 0 10 5.7" fill="var(--color-fb-icon)">
                    <path d="M5 5.7L0 0.7L0.7 0L5 4.3L9.3 0L10 0.7L5 5.7Z"/>
                </svg>
            </div>
        </div>
    </BaseNode>
</template>

<script setup>
import { ref } from 'vue';
import BaseNode from './BaseNode.vue';
import { TagIcon } from './icons';

const tagName = ref('');
const selectedScope = ref('Usar solo en este chatbot');
const showScopeDropdown = ref(false);

const toggleScopeDropdown = () => {
    showScopeDropdown.value = !showScopeDropdown.value;
};

const nodeConfig = {
    header: {
        iconColor: '#ff773d',
        icon: TagIcon,
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

<style scoped>
input::placeholder {
    color: var(--color-fb-input-placeholder);
}
</style>
