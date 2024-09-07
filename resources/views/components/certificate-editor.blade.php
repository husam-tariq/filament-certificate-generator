<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @php

        $canvasData = $getCanvasData();
        $options = $getOptions();
        $statePath = $getStatePath();

    @endphp

    <div
        ax-load
        ax-load-src="{{\Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('certificate-editor','husam-tariq/filament-certificate-generator')}}"

        x-data="certificateEditor({ state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},canvasData:{{$canvasData}},options:JSON.parse('{{ $getOptionsJson() }}') })"
        >
        <div class="flex flex-wrap items-center gap-3 fi-modal-footer-actions">
            <template x-for="(value, index) in options">
                <a @click="addText(index,value)" x-bind:disabled="true"
                   style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                   class="fi-btn add-text relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
                    <span class="fi-btn-label" x-text="value"></span>
                </a>
            </template>

            <a @click="addQr()" x-bind:disabled="true"
               style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
               class="fi-btn add-text relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
                <span class="fi-btn-label" >QR</span>
            </a>

        </div>
        <div id="textControls" hidden>
            <input id="ITextColor" type="color" style="width: 30px;height: 30px">


        </div>
        <canvas
            x-ref="containerRef"
            id="container">
        </canvas>
    </div>


</x-dynamic-component>
