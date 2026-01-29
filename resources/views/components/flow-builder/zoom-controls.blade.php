{{-- Zoom controls - exact Figma specs --}}
{{-- Position: bottom-[17px] right-[31px] --}}
<div
    class="absolute bottom-[17px] right-[31px] flex gap-[8px] items-center rounded-[8px] z-10"
    x-data="{ zoom: 100 }"
    @zoom-changed.window="zoom = $event.detail.zoom"
>
    {{-- Zoom percentage --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <div class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px]">
            <div class="flex flex-col font-semibold justify-center leading-[18px] text-[#364153] text-[14px]" style="font-family: 'Inter', sans-serif;">
                <p class="leading-[18px]" x-text="zoom + '%'">100%</p>
            </div>
        </div>
    </div>

    {{-- Center-to-fit button --}}
    <div class="bg-white flex items-center overflow-clip rounded-[8px]">
        <button
            type="button"
            class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] hover:bg-[#f3f4f6]"
            title="Centrar"
            x-on:click="window.dispatchEvent(new CustomEvent('fit-view'))"
        >
            <div class="overflow-clip relative size-[16px]">
                <svg class="size-full" viewBox="0 0 16 16" fill="none">
                    <path d="M6 2H2V6H3V3H6V2ZM2 10V14H6V13H3V10H2ZM10 13V14H14V10H13V13H10ZM13 6H14V2H10V3H13V6ZM10 7H6V9H8V11H6V13H8V11H10V9H8V7H10Z" fill="#364153"/>
                </svg>
            </div>
        </button>
    </div>
</div>
