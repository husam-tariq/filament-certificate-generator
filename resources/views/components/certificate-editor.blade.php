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
        <div class="flex flex-wrap items-center gap-3 fi-modal-footer-actions mb-4">
            <template x-for="(value, index) in options">
                <x-filament::button @click="addText(index,value)" x-text="value"></x-filament::button>
            </template>
            <x-filament::button @click="addQr()" >QR</x-filament::button>



        </div>
        <div id="textControls" hidden>
            <input id="ITextColor" type="color" style="width: 30px;height: 30px">


        </div>
        <canvas class="border-primary-600 border-2 rounded-md"
            x-ref="containerRef"
            id="container">
        </canvas>
    </div>


</x-dynamic-component>
