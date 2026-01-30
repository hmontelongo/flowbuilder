<template>
    <div
        class="file-upload-box"
        @click.stop="$emit('select')"
        @mousedown.stop
    >
        <!-- File type icons row -->
        <div class="file-icons-row">
            <component
                v-for="type in allowedTypes"
                :key="type"
                :is="fileTypeIcons[type]"
                class="file-icon"
            />
        </div>

        <!-- Helper text -->
        <span class="helper-text">{{ helperText }}</span>

        <!-- Formats link with tooltip -->
        <div class="formats-container">
            <button
                type="button"
                class="formats-link"
                @click.stop="showTooltip = !showTooltip"
                @mousedown.stop
            >
                Formatos aceptados
            </button>
            <div v-if="showTooltip" class="formats-tooltip">
                <div v-for="(formats, type) in acceptedFormatsDisplay" :key="type" class="format-group">
                    <span class="format-type">{{ formatTypeLabels[type] }}:</span>
                    <span class="format-list">{{ formats }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, markRaw } from 'vue';
import { ImageFileIcon, VideoFileIcon, StickerFileIcon, DocumentFileIcon, AudioFileIcon } from '../icons';

const props = defineProps({
    allowedTypes: {
        type: Array,
        default: () => ['image', 'video', 'sticker', 'document', 'audio'],
        validator: (value) => value.every(t => ['image', 'video', 'sticker', 'document', 'audio'].includes(t)),
    },
    helperText: {
        type: String,
        default: 'Selecciona o arrastra el tipo de archivo por adjuntar',
    },
});

defineEmits(['select']);

const showTooltip = ref(false);

// Map type names to icons
const fileTypeIcons = {
    image: markRaw(ImageFileIcon),
    video: markRaw(VideoFileIcon),
    sticker: markRaw(StickerFileIcon),
    document: markRaw(DocumentFileIcon),
    audio: markRaw(AudioFileIcon),
};

// Labels for format types
const formatTypeLabels = {
    image: 'Imagen',
    video: 'Video',
    sticker: 'Sticker',
    document: 'Documento',
    audio: 'Audio',
};

// Accepted formats per type (WhatsApp Cloud API compatible)
const acceptedFormats = {
    image: 'JPG, PNG, WEBP',
    video: 'MP4, 3GP',
    sticker: 'WEBP',
    document: 'PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT',
    audio: 'MP3, OGG, AMR, AAC, M4A',
};

// Only show formats for allowed types
const acceptedFormatsDisplay = computed(() => {
    const result = {};
    props.allowedTypes.forEach(type => {
        result[type] = acceptedFormats[type];
    });
    return result;
});
</script>

<style scoped>
.file-upload-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 4px;
    border: 1px solid var(--color-fb-input-border);
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
    transition: border-color 0.15s ease;
}

.file-upload-box:hover {
    border-color: var(--color-fb-input-border-focus);
}

.file-icons-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: var(--color-fb-neutral-500);
}

.file-icon {
    width: 16px;
    height: 16px;
}

.helper-text {
    font-size: var(--fb-text-xs);
    line-height: var(--fb-line-height-tight);
    color: var(--color-fb-input-placeholder);
    text-align: center;
}

.formats-container {
    position: relative;
    align-self: flex-end;
}

.formats-link {
    font-size: var(--fb-text-xs);
    line-height: var(--fb-line-height-tight);
    color: var(--color-fb-text-link);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.formats-link:hover {
    text-decoration: underline;
}

.formats-tooltip {
    position: absolute;
    right: 0;
    top: calc(100% + var(--fb-space-xs));
    background-color: var(--color-fb-neutral-800);
    color: var(--color-fb-neutral-0);
    padding: var(--fb-space-s);
    border-radius: var(--fb-space-xs);
    box-shadow: var(--fb-shadow-dropdown);
    min-width: 200px;
    z-index: 10;
}

.format-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
    margin-bottom: var(--fb-space-xs);
}

.format-group:last-child {
    margin-bottom: 0;
}

.format-type {
    font-size: var(--fb-text-xs);
    font-weight: 600;
    color: var(--color-fb-neutral-200);
}

.format-list {
    font-size: var(--fb-text-xs);
    color: var(--color-fb-neutral-300);
}
</style>
