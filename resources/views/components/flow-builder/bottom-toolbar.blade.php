{{-- Bottom toolbar - exact Figma specs --}}
{{-- Position: center-ish at bottom --}}
<div class="absolute bottom-[17px] left-1/2 -translate-x-1/2 flex gap-[8px] items-center z-10">
    {{-- Location--save (Variable) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Variable" x-data @click="$dispatch('add-block', { type: 'variable' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M8 1C5.2 1 3 3.2 3 6C3 9.5 8 15 8 15C8 15 13 9.5 13 6C13 3.2 10.8 1 8 1ZM8 14C6.8 12.5 4 8.8 4 6C4 3.8 5.8 2 8 2C10.2 2 12 3.8 12 6C12 8.8 9.2 12.5 8 14ZM6 5.5V7.5H7.5V9H8.5V7.5H10V6.5H8.5V5H7.5V6.5H6V5.5Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Bookmark (Tag) --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Etiqueta" x-data @click="$dispatch('add-block', { type: 'tag' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M11 1H5C4.4 1 4 1.4 4 2V15L8 12.5L12 15V2C12 1.4 11.6 1 11 1ZM11 13.5L8 11.5L5 13.5V2H11V13.5Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Events --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Eventos" x-data @click="$dispatch('add-block', { type: 'events' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M13 1H3C1.9 1 1 1.9 1 3V13C1 14.1 1.9 15 3 15H13C14.1 15 15 14.1 15 13V3C15 1.9 14.1 1 13 1ZM14 13C14 13.6 13.6 14 13 14H3C2.4 14 2 13.6 2 13V6H14V13ZM14 5H2V3C2 2.4 2.4 2 3 2H13C13.6 2 14 2.4 14 3V5ZM4 8H6V10H4V8ZM7 8H9V10H7V8ZM10 8H12V10H10V8Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>

    {{-- Data--categorical --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button type="button" class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]" title="Datos" x-data @click="$dispatch('add-block', { type: 'data' })">
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M4 2H7V7H4V2ZM5 3V6H6V3H5ZM9 2H12V7H9V2ZM10 3V6H11V3H10ZM4 9H7V14H4V9ZM5 10V13H6V10H5ZM9 9H12V14H9V9ZM10 10V13H11V10H10Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>
</div>
