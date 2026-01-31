<template>
    <BaseModifier :id="id" :data="data" v-slot="{ isInputConnected, isOutputConnected, onDelete, emitDataUpdate }">
        <div class="timer-modifier group">
            <!-- Left connector -->
            <Handle
                type="target"
                :position="Position.Left"
                class="modifier-handle modifier-handle-left"
                :class="{ 'modifier-handle-connected': isInputConnected }"
            />

            <!-- Icon -->
            <div class="timer-modifier-icon">
                <WaitIcon />
            </div>

            <!-- Duration input -->
            <input
                v-model.number="duration"
                type="number"
                min="1"
                class="timer-modifier-input"
                @input="updateData(emitDataUpdate)"
            />

            <!-- Unit select -->
            <select
                v-model="unit"
                class="timer-modifier-select"
                @change="updateData(emitDataUpdate)"
            >
                <option value="seconds">s</option>
                <option value="minutes">m</option>
                <option value="hours">h</option>
            </select>

            <!-- Delete button (visible on hover) -->
            <button
                type="button"
                class="timer-modifier-delete"
                @click.stop="onDelete"
                @mousedown.stop
            >
                <svg class="w-2.5 h-2.5" viewBox="0 0 8 8" fill="currentColor">
                    <path d="M7.5 1.205L6.795 0.5L4 3.295L1.205 0.5L0.5 1.205L3.295 4L0.5 6.795L1.205 7.5L4 4.705L6.795 7.5L7.5 6.795L4.705 4L7.5 1.205Z"/>
                </svg>
            </button>

            <!-- Right connector -->
            <Handle
                type="source"
                :position="Position.Right"
                class="modifier-handle modifier-handle-right"
                :class="{ 'modifier-handle-connected': isOutputConnected }"
            />
        </div>
    </BaseModifier>
</template>

<script setup>
import { ref } from 'vue';
import { Handle, Position } from '@vue-flow/core';
import BaseModifier from './BaseModifier.vue';
import { WaitIcon } from './icons';

const props = defineProps({
    id: { type: String, required: true },
    data: { type: Object, default: () => ({}) },
});

// Local state - initialize from persisted data
const duration = ref(props.data?.duration || 5);
const unit = ref(props.data?.unit || 'minutes');

const updateData = (emitDataUpdate) => {
    emitDataUpdate({ duration: duration.value, unit: unit.value });
};
</script>

<style scoped>
.timer-modifier {
    display: flex;
    align-items: center;
    gap: 4px;
    height: 28px;
    padding: 4px 6px 4px 4px;
    background: var(--color-fb-node-bg-normal, white);
    border: 1px solid var(--color-fb-node-border-normal, #e5e7eb);
    border-radius: 14px;
    position: relative;
    min-width: 100px;
    max-width: 160px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.timer-modifier:hover {
    border-color: var(--color-fb-node-border-edit, #3866ff);
}

.timer-modifier-icon {
    width: 20px;
    height: 20px;
    border-radius: 10px;
    background: var(--color-fb-node-wait, #f59e0b);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: white;
}

.timer-modifier-icon :deep(svg) {
    width: 12px;
    height: 12px;
}

.timer-modifier-input {
    width: 36px;
    border: none;
    outline: none;
    background: transparent;
    font-size: 12px;
    line-height: 16px;
    color: var(--color-fb-input-text, #1f2937);
    font-family: inherit;
    text-align: right;
    -moz-appearance: textfield;
}

.timer-modifier-input::-webkit-outer-spin-button,
.timer-modifier-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.timer-modifier-select {
    border: none;
    outline: none;
    background: transparent;
    font-size: 12px;
    line-height: 16px;
    color: var(--color-fb-input-text, #1f2937);
    font-family: inherit;
    cursor: pointer;
    padding: 0;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.timer-modifier-delete {
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: var(--color-fb-neutral-400, #9ca3af);
    border-radius: 4px;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.15s ease, background-color 0.15s ease, color 0.15s ease;
}

.timer-modifier:hover .timer-modifier-delete {
    opacity: 1;
}

.timer-modifier-delete:hover {
    background: #fef2f2;
    color: var(--color-fb-node-border-error, #ef4444);
}

/* Handle styling - positioned at edges */
.modifier-handle {
    width: 8px !important;
    height: 8px !important;
    background: white !important;
    border: 1px solid var(--color-fb-node-border-normal, #e5e7eb) !important;
    border-radius: 4px !important;
}

.modifier-handle-left {
    left: -4px !important;
}

.modifier-handle-right {
    right: -4px !important;
}

.modifier-handle-connected {
    border-color: var(--color-fb-node-border-edit, #3866ff) !important;
}

.modifier-handle:hover {
    border-color: var(--color-fb-node-border-edit, #3866ff) !important;
}
</style>
