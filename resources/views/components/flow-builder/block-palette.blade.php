{{-- Block palette - exact Figma specs --}}
{{-- First group: left: 23px, top: 135px --}}
<div class="absolute left-[23px] top-[135px] flex flex-col gap-[8px] items-start justify-center z-10">
    {{-- Lightning (Trigger) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Trigger" x-data @click="$dispatch('add-block', { type: 'trigger' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M9.5 1L4 9H7.5V15L13 7H9.5V1Z" stroke="#364153" stroke-width="1" fill="none"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Chat (Message) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Mensaje" x-data @click="$dispatch('add-block', { type: 'message' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M14 2H2V10H5V14L9 10H14V2ZM13 9H8.5L6 11.5V9H3V3H13V9Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- List--numbered (Menu) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Menú" x-data @click="$dispatch('add-block', { type: 'menu' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M2 3V4H3V3H2ZM5 3V4H14V3H5ZM2 7V8H3V7H2ZM5 7V8H14V7H5ZM2 11V12H3V11H2ZM5 11V12H14V11H5Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Customer-service (Transfer) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Transferir" x-data @click="$dispatch('add-block', { type: 'transfer' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M14 12.5V10C14 8.9 13.1 8 12 8H4C2.9 8 2 8.9 2 10V12.5H3V10C3 9.4 3.4 9 4 9H12C12.6 9 13 9.4 13 10V12.5H14ZM8 7C9.7 7 11 5.7 11 4C11 2.3 9.7 1 8 1C6.3 1 5 2.3 5 4C5 5.7 6.3 7 8 7ZM8 2C9.1 2 10 2.9 10 4C10 5.1 9.1 6 8 6C6.9 6 6 5.1 6 4C6 2.9 6.9 2 8 2ZM5 12H1V16H5V12ZM4 15H2V13H4V15ZM10 12H6V16H10V12ZM9 15H7V13H9V15ZM15 12H11V16H15V12ZM14 15H12V13H14V15Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- API (text) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="API" x-data @click="$dispatch('add-block', { type: 'api' })">
            <span class="text-[10px] font-semibold text-[#364153]" style="font-family: 'Inter', sans-serif;">API</span>
        </button>
    </div>

    {{-- AI (text) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="AI" x-data @click="$dispatch('add-block', { type: 'ai' })">
            <span class="text-[10px] font-semibold text-[#364153]" style="font-family: 'Inter', sans-serif;">AI</span>
        </button>
    </div>

    {{-- Flag --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Bandera" x-data @click="$dispatch('add-block', { type: 'flag' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M3 1V15H4V9H13L11 5L13 1H3ZM4 2H11L9.5 5L11 8H4V2Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>
</div>

{{-- Second group: left: 25px, top: 447px --}}
<div class="absolute left-[25px] top-[399px] flex flex-col gap-[8px] items-start justify-center z-10">
    {{-- Network-time-protocol (Wait) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Esperar" x-data @click="$dispatch('add-block', { type: 'wait' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M8 1C4.1 1 1 4.1 1 8C1 11.9 4.1 15 8 15C11.9 15 15 11.9 15 8C15 4.1 11.9 1 8 1ZM8 14C4.7 14 2 11.3 2 8C2 4.7 4.7 2 8 2C11.3 2 14 4.7 14 8C14 11.3 11.3 14 8 14ZM8.5 4H7.5V8.7L10.9 10.5L11.4 9.6L8.5 8.1V4Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Knowledge catalog --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Conocimiento" x-data @click="$dispatch('add-block', { type: 'knowledge' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M13 1H3C1.9 1 1 1.9 1 3V13C1 14.1 1.9 15 3 15H13C14.1 15 15 14.1 15 13V3C15 1.9 14.1 1 13 1ZM14 13C14 13.6 13.6 14 13 14H3C2.4 14 2 13.6 2 13V3C2 2.4 2.4 2 3 2H13C13.6 2 14 2.4 14 3V13ZM4 4H12V5H4V4ZM4 7H12V8H4V7ZM4 10H9V11H4V10Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Condition--point --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Condición" x-data @click="$dispatch('add-block', { type: 'condition' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M8 1L1 8L8 15L15 8L8 1ZM8 2.4L13.6 8L8 13.6L2.4 8L8 2.4ZM8 6C7.4 6 7 6.4 7 7C7 7.6 7.4 8 8 8C8.6 8 9 7.6 9 7C9 6.4 8.6 6 8 6Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Timer --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Temporizador" x-data @click="$dispatch('add-block', { type: 'timer' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M8 4C5.2 4 3 6.2 3 9C3 11.8 5.2 14 8 14C10.8 14 13 11.8 13 9C13 6.2 10.8 4 8 4ZM8 13C5.8 13 4 11.2 4 9C4 6.8 5.8 5 8 5C10.2 5 12 6.8 12 9C12 11.2 10.2 13 8 13ZM8.5 6H7.5V9.5L10 11L10.5 10.2L8.5 9V6ZM10 2H6V3H10V2ZM13.3 4.3L12.6 5L13.7 6.1L14.4 5.4L13.3 4.3Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Repeat --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Repetir" x-data @click="$dispatch('add-block', { type: 'repeat' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M12 2V1L15 3.5L12 6V5H4C3.4 5 3 5.4 3 6V8H2V6C2 4.9 2.9 4 4 4H12V2ZM4 14V15L1 12.5L4 10V11H12C12.6 11 13 10.6 13 10V8H14V10C14 11.1 13.1 12 12 12H4V14Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Reset--alt --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Reiniciar" x-data @click="$dispatch('add-block', { type: 'reset' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M8 3V1L4 4L8 7V5C10.2 5 12 6.8 12 9C12 11.2 10.2 13 8 13C5.8 13 4 11.2 4 9H2C2 12.3 4.7 15 8 15C11.3 15 14 12.3 14 9C14 5.7 11.3 3 8 3Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>
</div>
