{{-- Dev Toolbar - Development utilities, not user-facing --}}
<div class="fixed bottom-4 left-20 z-50 flex items-center gap-2 px-3 py-2 rounded-lg bg-neutral-800/90 border border-neutral-700 text-neutral-400 text-xs font-mono">
    <span class="text-neutral-500 select-none">Dev</span>
    <div class="w-px h-4 bg-neutral-600"></div>
    <button
        type="button"
        wire:click="clearCanvas"
        class="px-2 py-1 rounded bg-neutral-700 hover:bg-neutral-600 hover:text-neutral-300 transition-colors"
    >
        Clear Canvas
    </button>
    <button
        type="button"
        @click="$dispatch('toggle-error-state')"
        class="px-2 py-1 rounded bg-red-900/50 hover:bg-red-800/50 hover:text-red-300 text-red-400 transition-colors"
    >
        Toggle Error
    </button>
</div>
