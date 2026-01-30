<template>
    <div class="message-options-bar">
        <slot />
    </div>
</template>

<script setup>
</script>

<style scoped>
.message-options-bar {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: var(--fb-space-s);
    padding: var(--fb-space-xs) 0;
    background-color: var(--color-fb-neutral-50);
    border-radius: var(--fb-space-xs) var(--fb-space-xs) 0 0;
}
</style>
