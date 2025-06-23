<div
    x-cloak
    x-data="dropzone({
        _this: @this,
        uuid: @js($uuid),
        multiple: @js($multiple),
    })"
    @dragenter.prevent="onDragenter($event)"
    @dragleave.prevent="onDragleave($event)"
    @dragover.prevent="onDragover($event)"
    @drop.prevent="onDrop"
    class="block antialiased"
>
    <div class="flex flex-col items-start h-full w-full justify-center bg-transparent">
        <div
            @click="$refs.input.click()"
            class="border-2 border-dashed rounded-lg @if(!is_null($error)) border-red-500 @else border-gray-300 dark:border-gray-600 @endif transition-colors duration-300 ease-in-out w-full cursor-pointer"
            @mouseenter="isDragging = true"
            @dragenter="isDragging = true"
            @mouseleave="isDragging = false"
            @dragleave="isDragging = false"
            :style="{ 'border-color': isDragging ? '{{ $this->accentColor }}' : '' }"
        >
            <div>
                <div class="flex flex-col items-center bg-transparent justify-center py-4 h-full gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        <span
                            class="font-medium"
                            style="color: {{ $this->accentColor }};"
                        >
                            {{ __('Click to upload') }}
                        </span>
                        {{ __('or drag and drop') }}
                    </p>
                    <div class="flex gap-1 text-xs text-gray-600 dark:text-gray-400">
                        @php
                            $hasMaxFileSize = ! is_null($this->maxFileSize);
                            $hasMimes = ! empty($this->mimes);
                            $hasDimensions = ! empty($this->dimensions);
                        @endphp

                        @if($hasMimes)
                            <p>{{ Str::upper($this->mimes) }}</p>
                        @endif

                        @if($hasMaxFileSize)
                            <p>{{ __($hasMimes ? 'up to :size' : 'Up to :size', ['size' => \Illuminate\Support\Number::fileSize($this->maxFileSize * 1024)]) }}</p>
                        @endif

                        @if($hasDimensions)
                            <p>({{ __('Up to :dimensions', ['dimensions' => $this->dimensions]) }})</p>
                        @endif
                    </div>
                </div>
            </div>
            <input
                    x-ref="input"
                    wire:model="upload"
                    type="file"
                    class="hidden"
                    x-on:livewire-upload-start="isLoading = true"
                    x-on:livewire-upload-cancel="isLoading = false"
                    x-on:livewire-upload-finish="isLoading = false"
                    x-on:livewire-upload-error="console.log('livewire-dropzone upload error')"
                    @if(! is_null($this->accept)) accept="{{ $this->accept }}" @endif
                    @if($multiple === true) multiple @endif
            >
        </div>

        <div class="flex justify-between w-full mt-2">
            <div>
                @if(! is_null($error))
                    <h3 class="text-sm font-medium text-red-500">{{ $error }}</h3>
                @endif
            </div>
            <div x-show="isLoading" class="flex gap-1 items-center">
                <svg aria-hidden="true" width="15" height="15" class="text-gray-200 animate-spin dark:text-gray-700 fill-gray-800 dark:fill-gray-200" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                </svg>
                <span class="sr-only">Loading...</span>
                <div @click="cancelUpload" class="text-xs md:text-sm text-gray-800 dark:text-gray-200 hover:cursor-pointer underline">Cancel upload</div>
            </div>
        </div>

        @if(isset($files) && count($files) > 0)
        <div class="flex flex-wrap gap-x-10 gap-y-2 justify-start w-full mt-2">
            @foreach($files as $file)
                <div class="flex items-center gap-4 border border-gray-200 dark:border-gray-700 rounded-md p-1">
                    @if($this->isImageMime($file['extension']))
                    <div class="flex-shrink-0">
                        <img
                            class="h-12 w-12 rounded-md object-cover"
                            src="{{ $file['temporaryUrl'] }}"
                            @if(array_key_exists('name', $file))
                                alt="{{ $file['name'] }}"
                            @endif
                        >
                    </div>
                    @else
                    <div class="flex justify-center items-center w-14 h-14 bg-gray-100 dark:bg-gray-700 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-8 h-8 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        @if(array_key_exists('name', $file))
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-200 truncate">
                                {{ $file['name'] }}
                            </p>
                        @endif
                        @if(array_key_exists('size', $file))
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Illuminate\Support\Number::fileSize($file['size']) }}
                            </p>
                        @endif
                    </div>
                    <x-ui.cross-button
                        wire:click="removeFile('{{$file['dbField'] ?? null}}')"
                        @click="removeUpload('{{ $file['tmpFilename'] }}')"
                    />
                </div>
            @endforeach
        </div>
        @endif
    </div>

    @script
    <script>
        Alpine.data('dropzone', ({ _this, uuid, multiple }) => {
            return ({
                isDragging: false,
                isDropped: false,
                isLoading: false,

                onDrop(e) {
                    this.isDropped = true
                    this.isDragging = false

                    const file = multiple ? e.dataTransfer.files : e.dataTransfer.files[0]

                    const args = ['upload', file, () => {
                        // Upload completed
                        this.isLoading = false
                    }, (error) => {
                        // An error occurred while uploading
                        console.log('livewire-dropzone upload error', error);
                    }, () => {
                        // Uploading is in progress
                        this.isLoading = true
                    }];

                    // Upload file(s)
                    multiple ? _this.uploadMultiple(...args) : _this.upload(...args)
                },
                onDragenter() {
                    this.isDragging = true
                },
                onDragleave() {
                    this.isDragging = false
                },
                onDragover() {
                    this.isDragging = true
                },
                cancelUpload() {
                    _this.cancelUpload('upload')

                    this.isLoading = false
                },
                removeUpload(tmpFilename) {
                    // Dispatch an event to remove the temporarily uploaded file
                    _this.dispatch(uuid + ':fileRemoved', { tmpFilename })
                },
            });
        })
    </script>
    @endscript
</div>
