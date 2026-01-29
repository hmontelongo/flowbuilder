@props([
    'title' => 'Nombre del chatbot',
])

{{-- Canvas header - exact Figma specs --}}
<div class="absolute top-[-1px] left-[-1px] w-full flex items-center justify-between bg-white px-[16px] py-[8px]">
    {{-- Left section --}}
    <div class="flex items-center shrink-0">
        {{-- Back button --}}
        <div class="flex items-center overflow-clip rounded-[8px]">
            <div class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] cursor-pointer hover:bg-[#f3f4f6]">
                <div class="overflow-clip relative size-[16px]">
                    <svg class="size-full" viewBox="0 0 16 16" fill="none">
                        <path d="M8 14.5L1.5 8L8 1.5L8.7 2.2L3.4 7.5H14.5V8.5H3.4L8.7 13.8L8 14.5Z" fill="#364153"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Title with badge --}}
        <div class="flex gap-[4px] items-center">
            <p class="font-medium leading-[16px] text-[12px] text-[#1e2939]" style="font-family: 'Inter', sans-serif;">
                {{ $title }}
            </p>
            <div class="bg-[#d7d7d7] flex flex-col items-center justify-center overflow-clip px-[2px] py-[2px] rounded-[4px]">
                <p class="font-normal leading-[14px] text-[10px] text-white" style="font-family: 'Inter', sans-serif;">
                    Borrador
                </p>
            </div>
        </div>

        {{-- Undo button --}}
        <div class="flex items-center overflow-clip rounded-[8px]">
            <div class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] cursor-pointer hover:bg-[#f3f4f6]">
                <div class="overflow-clip relative size-[16px]">
                    <svg class="size-full" viewBox="0 0 16 16" fill="none">
                        <path d="M4.5 5H10C11.0609 5 12.0783 5.42143 12.8284 6.17157C13.5786 6.92172 14 7.93913 14 9C14 10.0609 13.5786 11.0783 12.8284 11.8284C12.0783 12.5786 11.0609 13 10 13H7V12H10C10.7956 12 11.5587 11.6839 12.1213 11.1213C12.6839 10.5587 13 9.79565 13 9C13 8.20435 12.6839 7.44129 12.1213 6.87868C11.5587 6.31607 10.7956 6 10 6H4.5V8L1 5.5L4.5 3V5Z" fill="#364153"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Redo button --}}
        <div class="flex items-center overflow-clip rounded-[8px]">
            <div class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] cursor-pointer hover:bg-[#f3f4f6]">
                <div class="overflow-clip relative size-[16px]">
                    <svg class="size-full" viewBox="0 0 16 16" fill="none">
                        <path d="M11.5 5H6C4.93913 5 3.92172 5.42143 3.17157 6.17157C2.42143 6.92172 2 7.93913 2 9C2 10.0609 2.42143 11.0783 3.17157 11.8284C3.92172 12.5786 4.93913 13 6 13H9V12H6C5.20435 12 4.44129 11.6839 3.87868 11.1213C3.31607 10.5587 3 9.79565 3 9C3 8.20435 3.31607 7.44129 3.87868 6.87868C4.44129 6.31607 5.20435 6 6 6H11.5V8L15 5.5L11.5 3V5Z" fill="#364153"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Right section --}}
    <div class="flex gap-[4px] items-center shrink-0">
        {{-- Search button --}}
        <div class="bg-white flex items-center overflow-clip rounded-[8px]">
            <div class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] cursor-pointer hover:bg-[#f3f4f6]">
                <div class="overflow-clip relative size-[16px]">
                    <svg class="size-full" viewBox="0 0 16 16" fill="none">
                        <path d="M6.5 12C3.46243 12 1 9.53757 1 6.5C1 3.46243 3.46243 1 6.5 1C9.53757 1 12 3.46243 12 6.5C12 7.74832 11.5841 8.89778 10.8863 9.82469L14.5303 13.4697L13.4697 14.5303L9.82469 10.8863C8.89778 11.5841 7.74832 12 6.5 12ZM6.5 2.5C4.29086 2.5 2.5 4.29086 2.5 6.5C2.5 8.70914 4.29086 10.5 6.5 10.5C8.70914 10.5 10.5 8.70914 10.5 6.5C10.5 4.29086 8.70914 2.5 6.5 2.5Z" fill="#364153"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Run button group --}}
        <div class="bg-[#efefef] flex items-center px-[4px]">
            <p class="font-medium leading-[16px] text-[12px] text-[#1e2939]" style="font-family: 'Inter', sans-serif;">
                Run
            </p>
            <div class="flex items-center overflow-clip rounded-[8px]">
                <div class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] cursor-pointer hover:bg-[#e5e5e5]">
                    <div class="overflow-clip relative size-[16px]">
                        <svg class="size-full" viewBox="0 0 16 16" fill="none">
                            <path d="M4 2V14L14 8L4 2ZM5 4.2L11.5 8L5 11.8V4.2Z" fill="#364153"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Settings button --}}
        <div class="bg-white flex items-center overflow-clip rounded-[8px]">
            <div class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] cursor-pointer hover:bg-[#f3f4f6]">
                <div class="overflow-clip relative size-[16px]">
                    <svg class="size-full" viewBox="0 0 16 16" fill="none">
                        <path d="M14 4H6V5H14V4ZM14 7H9V8H14V7ZM14 10H6V11H14V10ZM4 3C3.73478 3 3.48043 3.10536 3.29289 3.29289C3.10536 3.48043 3 3.73478 3 4C3 4.26522 3.10536 4.51957 3.29289 4.70711C3.48043 4.89464 3.73478 5 4 5C4.26522 5 4.51957 4.89464 4.70711 4.70711C4.89464 4.51957 5 4.26522 5 4C5 3.73478 4.89464 3.48043 4.70711 3.29289C4.51957 3.10536 4.26522 3 4 3ZM7 6C6.73478 6 6.48043 6.10536 6.29289 6.29289C6.10536 6.48043 6 6.73478 6 7C6 7.26522 6.10536 7.51957 6.29289 7.70711C6.48043 7.89464 6.73478 8 7 8C7.26522 8 7.51957 7.89464 7.70711 7.70711C7.89464 7.51957 8 7.26522 8 7C8 6.73478 7.89464 6.48043 7.70711 6.29289C7.51957 6.10536 7.26522 6 7 6ZM4 9C3.73478 9 3.48043 9.10536 3.29289 9.29289C3.10536 9.48043 3 9.73478 3 10C3 10.2652 3.10536 10.5196 3.29289 10.7071C3.48043 10.8946 3.73478 11 4 11C4.26522 11 4.51957 10.8946 4.70711 10.7071C4.89464 10.5196 5 10.2652 5 10C5 9.73478 4.89464 9.48043 4.70711 9.29289C4.51957 9.10536 4.26522 9 4 9Z" fill="#364153"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Share button --}}
        <div class="bg-white flex items-center overflow-clip rounded-[8px]">
            <div class="flex gap-[8px] h-[32px] items-center justify-center px-[8px] py-[4px] rounded-[8px] cursor-pointer hover:bg-[#f3f4f6]">
                <div class="overflow-clip relative size-[16px]">
                    <svg class="size-full" viewBox="0 0 16 16" fill="none">
                        <path d="M12 10C11.4 10 10.9 10.2 10.5 10.6L6.4 8.2C6.5 7.8 6.5 7.4 6.4 7L10.3 4.8C10.8 5.2 11.4 5.5 12 5.5C13.4 5.5 14.5 4.4 14.5 3C14.5 1.6 13.4 0.5 12 0.5C10.6 0.5 9.5 1.6 9.5 3C9.5 3.2 9.5 3.4 9.6 3.6L5.7 5.8C5.2 5.4 4.6 5.1 4 5.1C2.6 5.1 1.5 6.2 1.5 7.6C1.5 9 2.6 10.1 4 10.1C4.6 10.1 5.2 9.8 5.7 9.4L9.6 11.6C9.5 11.8 9.5 12 9.5 12.2C9.5 13.6 10.6 14.7 12 14.7C13.4 14.7 14.5 13.6 14.5 12.2C14.5 10.8 13.4 10 12 10ZM12 1.5C12.8 1.5 13.5 2.2 13.5 3C13.5 3.8 12.8 4.5 12 4.5C11.2 4.5 10.5 3.8 10.5 3C10.5 2.2 11.2 1.5 12 1.5ZM4 9C3.2 9 2.5 8.3 2.5 7.5C2.5 6.7 3.2 6 4 6C4.8 6 5.5 6.7 5.5 7.5C5.5 8.3 4.8 9 4 9ZM12 13.5C11.2 13.5 10.5 12.8 10.5 12C10.5 11.2 11.2 10.5 12 10.5C12.8 10.5 13.5 11.2 13.5 12C13.5 12.8 12.8 13.5 12 13.5Z" fill="#364153"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
