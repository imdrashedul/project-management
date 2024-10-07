@props([
    'importRoute' => null,
    'acceptedMimes' => 'text/csv',
    'placeholder' => 'Drag file or click here to import',
    'uploadBtnText' => 'Import',
    'maxFilesize' => \App\File\FileUploadService::getMaxUploadSize(),
    'cancelBtnText' => 'Cancel',
])


@if (!empty($importRoute))
    <div x-data="initFileUpload({
        'importRoute': `{{ $importRoute }}`,
        'acceptedMimes': `{{ $acceptedMimes }}`,
        'maxFilesize': `{{ $maxFilesize }}`
    })" class="border-dashed border-2 border-gray-400 p-4 rounded">
        <form x-on:submit.prevent="uploadFile" enctype="multipart/form-data" class="text-center">
            <div x-on:click="$refs.fileInput.click()" x-on:dragover.prevent="dragOver" x-on:dragleave.prevent="dragLeave" x-on:drop.prevent="dropFile"
                class="p-10 border-2 border-dashed bg-gray-100 cursor-pointer" :class="{ 'border-purple-500 rounded bg-purple-200': dragging }">
                <template x-if="file">
                    <div>
                        <p class="font-bold">File Selected:</p>
                        <p x-text="file.name"></p>
                        <p x-text="file.readableSize"></p>
                        <p x-text="file.typeName"></p>

                    </div>
                </template>
                <template x-if="!file">
                    <div>
                        <p class="mb-2 text-slate-500" :class="{ 'text-purple-800': dragging }"><i class="fa-solid fa-file-import"></i></p>
                        <p class="mb-0 text-slate-400" :class="{ 'text-purple-800': dragging }">{{ $placeholder }}</p>
                    </div>
                </template>
                <template x-if="errorMessage">
                    <p class="text-red-500" x-text="errorMessage"></p>
                </template>
            </div>

            <div x-show="progressVisible" class="w-full bg-gray-200 rounded-full h-2.5 mt-4" x-cloak>
                <div class="bg-blue-500 h-2.5 rounded-full" :style="{ width: `${progress}%` }"></div>
            </div>

            <div x-show="file" class="mt-4 flex justify-center space-x-4" x-cloak>
                <button type="button" @click="clearFile" class="px-4 py-1 bg-red-500 text-white rounded">{{ $cancelBtnText }}</button>
                <button type="submit" class="px-4 py-1 bg-green-800 text-white rounded">{{ $uploadBtnText }}</button>
            </div>

            <div x-show="uploadedResponse" class="mt-4" x-cloak>
                <p x-show="uploadedResponse && uploadedResponse.status==200" class="text-green-800">File uploaded successfully</p>
                <p x-show="uploadedResponse && uploadedResponse.status==200 && _.has(uploadedResponse.data, 'message')" class="text-blue-800" x-text="uploadedResponse ? uploadedResponse.data.message : ''"></p>
                <p x-show="uploadedResponse && uploadedResponse.status!=200" class="text-red-800">File not uploaded!</p>
            </div>

            <input type="file" x-ref="fileInput" accept="{{ $acceptedMimes }}" class="hidden" @change="fileChosen">
        </form>
    </div>
@endif
