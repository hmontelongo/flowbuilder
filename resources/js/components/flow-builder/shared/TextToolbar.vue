<template>
    <div class="flex items-center gap-0.5">
        <button
            type="button"
            class="toolbar-btn"
            title="Emoji"
            @click.stop="$emit('emoji')"
            @mousedown.stop
        >
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1"/>
                <circle cx="5.5" cy="6.5" r="1" fill="currentColor"/>
                <circle cx="10.5" cy="6.5" r="1" fill="currentColor"/>
                <path d="M5 10C5.5 11 6.5 11.5 8 11.5C9.5 11.5 10.5 11 11 10" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
            </svg>
        </button>
        <button
            type="button"
            class="toolbar-btn"
            title="Mención"
            @click.stop="$emit('mention')"
            @mousedown.stop
        >
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none">
                <circle cx="8" cy="8" r="3" stroke="currentColor" stroke-width="1"/>
                <path d="M11 8C11 5.5 9.5 3.5 8 3.5C5.5 3.5 3.5 5.5 3.5 8C3.5 10.5 5.5 12.5 8 12.5H11" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
            </svg>
        </button>
        <button
            type="button"
            class="toolbar-btn"
            title="Negrita"
            @click.stop="$emit('bold')"
            @mousedown.stop
        >
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                <path d="M4 3h5c1.1 0 2 .4 2.6 1 .6.6 1 1.4 1 2.3 0 .7-.2 1.3-.6 1.8.6.5 1 1.3 1 2.2 0 .9-.4 1.7-1 2.3-.6.6-1.5 1-2.5 1H4V3zm2 4.5h2.5c.4 0 .7-.1.9-.3.2-.2.3-.5.3-.8 0-.3-.1-.6-.3-.8-.2-.2-.5-.3-.9-.3H6v2.2zm0 4.2h3c.4 0 .8-.1 1-.4.2-.2.4-.5.4-.9 0-.4-.1-.7-.4-.9-.2-.3-.6-.4-1-.4H6v2.6z"/>
            </svg>
        </button>
        <button
            type="button"
            class="toolbar-btn"
            title="Cursiva"
            @click.stop="$emit('italic')"
            @mousedown.stop
        >
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                <path d="M10 3H6v1h1.5l-2 8H4v1h4v-1H6.5l2-8H10V3z"/>
            </svg>
        </button>
        <button
            type="button"
            class="toolbar-btn"
            title="Tachado"
            @click.stop="$emit('strikethrough')"
            @mousedown.stop
        >
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="currentColor">
                <path d="M3 8h10v1H3V8z"/>
                <path d="M5.5 6c0-.8.7-1.5 2.5-1.5 1 0 1.7.3 2 .8l1-1c-.6-.8-1.6-1.3-3-1.3-2.5 0-3.5 1.2-3.5 2.5 0 .4.1.8.3 1h2c-.2-.2-.3-.3-.3-.5zm5 4c0 .8-.7 1.5-2.5 1.5-1 0-1.7-.3-2-.8l-1 1c.6.8 1.6 1.3 3 1.3 2.5 0 3.5-1.2 3.5-2.5 0-.4-.1-.8-.3-1h-2c.2.2.3.3.3.5z"/>
            </svg>
        </button>
    </div>
</template>

<script setup>
defineEmits(['emoji', 'mention', 'bold', 'italic', 'strikethrough']);
</script>

<style scoped>
.toolbar-btn {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2px;
    border-radius: var(--fb-space-xs);
    color: var(--color-fb-neutral-600);
    background: transparent;
    border: none;
    cursor: pointer;
    transition: background-color 0.15s ease;
}

.toolbar-btn:hover {
    background-color: var(--color-fb-neutral-200);
}
</style>
