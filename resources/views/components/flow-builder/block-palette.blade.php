{{-- Block palette - single continuous column of icon buttons --}}
{{-- All buttons uniform with consistent 8px gap --}}
<div class="absolute left-[16px] top-[120px] flex flex-col gap-2 z-10">
    {{-- Group 1: Essential blocks --}}
    <div class="flex flex-col gap-2">
        {{-- Canal --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Canal" @click="$dispatch('add-block', { type: 'canal' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M13.5 2.5a2 2 0 0 0-1.75 1h-2.5a4 4 0 1 0-1 4.9v2.35a1.5 1.5 0 1 0 1 0V8.4a4 4 0 0 0 1.75-1.15l1.6 1.6a2 2 0 1 0 .7-.7l-1.6-1.6A4 4 0 0 0 12.25 4.5h2.5a2 2 0 1 0-.25-2h-1zm-9 2a3 3 0 1 1 3 3 3 3 0 0 1-3-3z"/>
            </svg>
        </button>

        {{-- Trigger --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Trigger" @click="$dispatch('add-block', { type: 'trigger' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="none" stroke="#364153" stroke-width="1.2">
                <path d="M9 2L4 9h4v5l5-7H9V2z"/>
            </svg>
        </button>

        {{-- Message --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Mensaje" @click="$dispatch('add-block', { type: 'message' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M2 2h12v8H6l-3 3v-3H2V2zm1 1v6h1v2l2-2h7V3H3z"/>
            </svg>
        </button>

        {{-- Menu --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Menú" @click="$dispatch('add-block', { type: 'menu' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M2 4h2v1H2V4zm4 0h8v1H6V4zM2 7.5h2v1H2v-1zm4 0h8v1H6v-1zM2 11h2v1H2v-1zm4 0h8v1H6v-1z"/>
            </svg>
        </button>

        {{-- Transfer --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Transferir" @click="$dispatch('add-block', { type: 'transfer' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M8 7a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm0-5a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM4 9a2 2 0 0 0-2 2v2h1v-2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v2h1v-2a2 2 0 0 0-2-2H4z"/>
            </svg>
        </button>

        {{-- API --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="API" @click="$dispatch('add-block', { type: 'api' })">
            <span class="text-[10px] font-semibold text-[#364153]">API</span>
        </button>

        {{-- AI --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="AI" @click="$dispatch('add-block', { type: 'ai' })">
            <span class="text-[10px] font-semibold text-[#364153]">AI</span>
        </button>

        {{-- Flag --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Bandera" @click="$dispatch('add-block', { type: 'flag' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M3 2v12h1v-5h8l-2-3.5L12 2H3zm1 1h6.5l-1.5 2.5 1.5 2.5H4V3z"/>
            </svg>
        </button>
    </div>

    {{-- Spacer between groups --}}
    <div class="h-2"></div>

    {{-- Group 2: Logic blocks --}}
    <div class="flex flex-col gap-2">
        {{-- Wait --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Esperar" @click="$dispatch('add-block', { type: 'wait' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 13A6 6 0 1 1 8 2a6 6 0 0 1 0 12zm.5-9.5h-1v4l3.5 2 .5-.87-3-1.73V4.5z"/>
            </svg>
        </button>

        {{-- Knowledge --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Conocimiento" @click="$dispatch('add-block', { type: 'knowledge' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M3 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H3zm0 1h10a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm1 2v1h8V4H4zm0 3v1h8V7H4zm0 3v1h5v-1H4z"/>
            </svg>
        </button>

        {{-- Condition --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Condición" @click="$dispatch('add-block', { type: 'condition' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M8 1L1 8l7 7 7-7-7-7zm0 1.5L13.5 8 8 13.5 2.5 8 8 2.5zM7 7a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"/>
            </svg>
        </button>

        {{-- Timer --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Temporizador" @click="$dispatch('add-block', { type: 'timer' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M6 1v1h4V1H6zm2 3a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 1a4 4 0 1 1 0 8 4 4 0 0 1 0-8zm-.5 1v3l2.5 1.5.5-.87-2-1.2V6h-1zm5.35-2.35l-.7.7 1.4 1.4.7-.7-1.4-1.4z"/>
            </svg>
        </button>

        {{-- Repeat --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Repetir" @click="$dispatch('add-block', { type: 'repeat' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M11 2v1h-6a2 2 0 0 0-2 2v2H2V5a3 3 0 0 1 3-3h6V1l3 2-3 2V4zm-6 12v-1h6a2 2 0 0 0 2-2v-2h1v2a3 3 0 0 1-3 3H5v1l-3-2 3-2v1z"/>
            </svg>
        </button>

        {{-- Reset --}}
        <button type="button" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg hover:bg-gray-100" title="Reiniciar" @click="$dispatch('add-block', { type: 'reset' })">
            <svg class="w-4 h-4" viewBox="0 0 16 16" fill="#364153">
                <path d="M8 3V1L4 4l4 3V5a4 4 0 1 1-4 4H2a6 6 0 1 0 6-6z"/>
            </svg>
        </button>
    </div>
</div>
