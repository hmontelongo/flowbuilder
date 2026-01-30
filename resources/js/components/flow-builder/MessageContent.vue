<template>
    <div class="message-content flex flex-col gap-1 w-full">
        <!-- Text message -->
        <template v-if="message.type === 'text'">
            <div class="node-input">
                <div class="flex flex-col gap-2 w-full">
                    <textarea
                        ref="textareaRef"
                        v-model="content"
                        class="node-text-xs node-textarea"
                        placeholder="Escribe tu mensaje..."
                        @click.stop
                        @mousedown.stop
                        @input="updateCharCount"
                    ></textarea>
                    <div class="flex items-end justify-between gap-2 w-full">
                        <TextToolbar />
                        <span class="node-text-muted">{{ charCount.toLocaleString() }}</span>
                    </div>
                </div>
            </div>
        </template>

        <!-- Attachment message -->
        <template v-else-if="message.type === 'attachment'">
            <FileUploadBox
                :allowed-types="['image', 'video', 'sticker', 'document', 'audio']"
                @select="selectFile"
            />
        </template>

        <!-- Button message (Interactive Reply Buttons) -->
        <template v-else-if="message.type === 'button'">
            <!-- Options bar for optional header/footer + button counter -->
            <MessageOptionsBar>
                <ToggleButtonGroup
                    v-model="buttonHeaderType"
                    :options="headerOptions"
                />
                <ToggleIconButton
                    v-model="buttonFooterEnabled"
                    label="Pie de página"
                >
                    <FooterIcon />
                </ToggleIconButton>
                <AddButtonCounter
                    :remaining="3 - buttons.length"
                    @add="addButton"
                />
            </MessageOptionsBar>

            <!-- Header input (conditional based on selection) -->
            <NodeInput
                v-if="buttonHeaderType === 'text'"
                v-model="buttonHeaderText"
                placeholder="Encabezado (opcional)"
            />
            <FileUploadBox
                v-else-if="buttonHeaderType === 'attachment'"
                :allowed-types="['image', 'video', 'document']"
                @select="selectButtonFile"
            />

            <!-- Body textarea with char counter -->
            <div class="node-input">
                <div class="flex flex-col gap-2 w-full">
                    <textarea
                        v-model="buttonBody"
                        class="node-text-xs node-textarea"
                        placeholder="Texto del mensaje..."
                        maxlength="1024"
                        @click.stop
                        @mousedown.stop
                    ></textarea>
                    <div class="flex items-end justify-end">
                        <span class="node-text-muted">{{ (1024 - buttonBody.length).toLocaleString() }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer input (conditional) -->
            <NodeInput
                v-if="buttonFooterEnabled"
                v-model="buttonFooter"
                placeholder="Pie de página (opcional)"
            />

            <!-- Buttons list (each button is an output - blue border indicates output) -->
            <div v-if="buttons.length > 0" class="flex flex-col gap-1">
                <ButtonInput
                    v-for="(btn, idx) in buttons"
                    :key="btn.id"
                    v-model="btn.title"
                    :button-id="btn.id"
                    :index="idx"
                    :placeholder="`Botón ${idx + 1}`"
                    :max-length="20"
                    variant="output"
                    @delete="deleteButton(idx)"
                    @reorder="reorderButtons"
                />
            </div>
        </template>

        <!-- Link message (CTA URL Button) -->
        <template v-else-if="message.type === 'link'">
            <!-- Options bar for optional header/footer -->
            <MessageOptionsBar>
                <!-- Header toggle group (text vs attachment) -->
                <ToggleButtonGroup
                    v-model="ctaHeaderType"
                    :options="headerOptions"
                />
                <!-- Footer toggle -->
                <ToggleIconButton
                    v-model="ctaFooterEnabled"
                    label="Pie de página"
                >
                    <FooterIcon />
                </ToggleIconButton>
            </MessageOptionsBar>

            <!-- Header input (conditional based on selection) -->
            <NodeInput
                v-if="ctaHeaderType === 'text'"
                v-model="ctaHeaderText"
                placeholder="Encabezado (opcional)"
            />
            <!-- Header attachment (image, video, document allowed for CTA headers) -->
            <FileUploadBox
                v-else-if="ctaHeaderType === 'attachment'"
                :allowed-types="['image', 'video', 'document']"
                @select="selectCtaFile"
            />

            <!-- Body textarea (reusing existing text message pattern) -->
            <div class="node-input">
                <div class="flex flex-col gap-2 w-full">
                    <textarea
                        v-model="ctaBody"
                        class="node-text-xs node-textarea"
                        placeholder="Escribe un mensaje"
                        @click.stop
                        @mousedown.stop
                    ></textarea>
                    <div class="flex items-end justify-end">
                        <span class="node-text-muted">{{ (1024 - ctaBody.length).toLocaleString() }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer input (conditional) -->
            <NodeInput
                v-if="ctaFooterEnabled"
                v-model="ctaFooter"
                placeholder="Pie de página (opcional)"
            />

            <!-- Button title with launch icon (blue border indicates output) -->
            <OutputNodeInput
                v-model="ctaButtonText"
                :button-id="ctaButtonId"
                placeholder="Título del botón"
            >
                <template #leftIcon>
                    <LaunchIcon />
                </template>
            </OutputNodeInput>

            <!-- URL input with link icon -->
            <NodeInput v-model="ctaUrl" placeholder="https://ejemplo.com">
                <template #leftIcon>
                    <LinkMessageIcon />
                </template>
            </NodeInput>
        </template>

        <!-- Location message -->
        <template v-else-if="message.type === 'location'">
            <NodeInput v-model="latitude" placeholder="Latitud" />
            <NodeInput v-model="longitude" placeholder="Longitud" />
            <NodeInput v-model="locationName" placeholder="Nombre del lugar (opcional)" />
        </template>

        <!-- List message -->
        <template v-else-if="message.type === 'list'">
            <div class="node-input">
                <textarea
                    v-model="content"
                    class="node-text-xs node-textarea"
                    placeholder="Texto del mensaje..."
                    @click.stop
                    @mousedown.stop
                ></textarea>
            </div>
            <NodeInput v-model="listButtonText" placeholder="Texto del botón de lista" />
            <div class="flex flex-col gap-1">
                <NodeInput
                    v-for="(item, idx) in listItems"
                    :key="idx"
                    v-model="listItems[idx]"
                    :placeholder="`Opción ${idx + 1}`"
                />
                <button
                    v-if="listItems.length < 10"
                    type="button"
                    class="node-input flex items-center justify-center gap-1"
                    @click.stop="addListItem"
                    @mousedown.stop
                >
                    <svg class="w-3 h-3" viewBox="0 0 12 12" :style="{ fill: 'var(--color-fb-neutral-500)' }">
                        <path d="M6.5 1H5.5V5.5H1V6.5H5.5V11H6.5V6.5H11V5.5H6.5V1Z"/>
                    </svg>
                    <span class="node-text-xs node-text-placeholder">Agregar opción</span>
                </button>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, markRaw, inject, watch, onMounted, onUnmounted } from 'vue';
import { NodeInput, TextToolbar, MessageOptionsBar, ToggleButtonGroup, ToggleIconButton, FileUploadBox, ButtonInput, AddButtonCounter, OutputNodeInput } from './shared';
import { FooterIcon, LaunchIcon, LinkMessageIcon, AttachmentIcon, HeaderTextIcon } from './icons';

const props = defineProps({
    message: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:message']);

// Inject the output buttons registry from MessageNode
const outputButtonsRegistry = inject('outputButtonsRegistry', null);

// Content state
const content = ref(props.message.content || '');
const textareaRef = ref(null);

// CTA URL message state
const headerOptions = [
    { id: 'text', icon: markRaw(HeaderTextIcon), label: 'Texto' },
    { id: 'attachment', icon: markRaw(AttachmentIcon), label: 'Archivo adjunto' },
];
const ctaHeaderType = ref(null); // 'text', 'attachment', or null
const ctaHeaderText = ref('');
const ctaFileName = ref('');
const ctaBody = ref('');
const ctaFooterEnabled = ref(false);
const ctaFooter = ref('');
const ctaButtonText = ref('');
const ctaUrl = ref('');

const selectCtaFile = () => {
    console.log('CTA file selection not implemented');
};

// Character count for text messages (WhatsApp limit: 4096)
const MAX_CHARS = 4096;
const charCount = computed(() => MAX_CHARS - content.value.length);

const updateCharCount = () => {
    if (content.value.length > MAX_CHARS) {
        content.value = content.value.substring(0, MAX_CHARS);
    }
};

// Attachment state
const fileName = ref('');
const selectFile = () => {
    console.log('File selection not implemented');
};

// Button message state (Interactive Reply Buttons)
const buttonHeaderType = ref(null); // 'text', 'attachment', or null
const buttonHeaderText = ref('');
const buttonBody = ref('');
const buttonFooterEnabled = ref(false);
const buttonFooter = ref('');
const buttons = ref([
    { id: crypto.randomUUID(), title: '' },
]);

const addButton = () => {
    if (buttons.value.length < 3) {
        buttons.value.push({ id: crypto.randomUUID(), title: '' });
    }
};

const deleteButton = (index) => {
    if (buttons.value.length > 1) {
        buttons.value.splice(index, 1);
    }
};

const reorderButtons = ({ from, to }) => {
    const item = buttons.value.splice(from, 1)[0];
    buttons.value.splice(to, 0, item);
};

const selectButtonFile = () => {
    console.log('Button header file selection not implemented');
};

// CTA button ID for link message type
const ctaButtonId = ref(crypto.randomUUID());

// Register output buttons with parent MessageNode
const updateOutputRegistry = () => {
    if (!outputButtonsRegistry) return;

    if (props.message.type === 'button') {
        outputButtonsRegistry.updateButtons(props.message.id, buttons.value);
    } else if (props.message.type === 'link') {
        outputButtonsRegistry.updateButtons(props.message.id, [{ id: ctaButtonId.value }]);
    }
};

// Watch for button changes and update registry
watch(buttons, updateOutputRegistry, { deep: true });

// Register on mount, unregister on unmount
onMounted(() => {
    updateOutputRegistry();
});

onUnmounted(() => {
    if (outputButtonsRegistry) {
        outputButtonsRegistry.updateButtons(props.message.id, []);
    }
});

// Location state
const latitude = ref('');
const longitude = ref('');
const locationName = ref('');

// List message state
const listButtonText = ref('');
const listItems = ref(['']);
const addListItem = () => {
    if (listItems.value.length < 10) {
        listItems.value.push('');
    }
};

// Serialize message state based on type
const serializeMessage = () => {
    const base = { id: props.message.id, type: props.message.type };

    switch (props.message.type) {
        case 'text':
            return { ...base, content: { text: content.value } };
        case 'button':
            return { ...base, content: {
                headerType: buttonHeaderType.value,
                headerText: buttonHeaderText.value,
                body: buttonBody.value,
                footerEnabled: buttonFooterEnabled.value,
                footer: buttonFooter.value,
                buttons: buttons.value.map(b => ({ id: b.id, title: b.title })),
            }};
        case 'link':
            return { ...base, content: {
                headerType: ctaHeaderType.value,
                headerText: ctaHeaderText.value,
                body: ctaBody.value,
                footerEnabled: ctaFooterEnabled.value,
                footer: ctaFooter.value,
                buttonText: ctaButtonText.value,
                buttonId: ctaButtonId.value,
                url: ctaUrl.value,
            }};
        case 'location':
            return { ...base, content: {
                latitude: latitude.value,
                longitude: longitude.value,
                name: locationName.value,
            }};
        case 'list':
            return { ...base, content: {
                text: content.value,
                buttonText: listButtonText.value,
                items: [...listItems.value],
            }};
        case 'attachment':
            return { ...base, content: { fileName: fileName.value } };
        default:
            return base;
    }
};

// Emit message updates when state changes
// Group all state refs by message type for watching
const textState = [content];
const buttonState = [buttonHeaderType, buttonHeaderText, buttonBody, buttonFooterEnabled, buttonFooter, buttons];
const linkState = [ctaHeaderType, ctaHeaderText, ctaBody, ctaFooterEnabled, ctaFooter, ctaButtonText, ctaUrl];
const locationState = [latitude, longitude, locationName];
const listState = [content, listButtonText, listItems];
const attachmentState = [fileName];

// Single watcher for all state - emits serialized message on any change
watch(
    () => [
        content.value,
        buttonHeaderType.value, buttonHeaderText.value, buttonBody.value,
        buttonFooterEnabled.value, buttonFooter.value, buttons.value,
        ctaHeaderType.value, ctaHeaderText.value, ctaBody.value,
        ctaFooterEnabled.value, ctaFooter.value, ctaButtonText.value, ctaUrl.value,
        latitude.value, longitude.value, locationName.value,
        listButtonText.value, listItems.value,
        fileName.value,
    ],
    () => {
        emit('update:message', serializeMessage());
    },
    { deep: true }
);
</script>

<style scoped>
.node-textarea {
    width: 100%;
    min-height: 48px;
    outline: none;
    resize: none;
    background: transparent;
    border: none;
}
</style>
