{{--
    Simple demo view for Flux Filemanager Editor
    
    This is a minimal example showing just the editor
    without complex layout or additional fields.
--}}

<div class="mx-auto max-w-4xl space-y-6 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Editor Demo</h1>

        <button wire:click="save" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
            Save
        </button>
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="rounded-lg bg-green-100 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Editor --}}
    <div class="rounded-lg bg-white p-6 shadow">
        <label class="mb-2 block text-sm font-medium text-gray-700">
            Content
        </label>

        <x-flux-filemanager-editor wire:model="content" toolbar="full" :rows="15" />

        <p class="mt-2 text-sm text-gray-500">
            Try these features:
            • Click 🖼️ to upload images
            • Click 🔗 to add file links
            • Drag & drop images directly into the editor
            • Paste screenshots with Cmd/Ctrl + V
            • Single click on images to resize
            • Double click on images to edit details
        </p>
    </div>

    {{-- Preview --}}
    <div class="rounded-lg bg-white p-6 shadow">
        <h2 class="mb-4 text-lg font-semibold">Preview</h2>

        <div class="prose max-w-none">
            {!! $content !!}
        </div>
    </div>
</div>
