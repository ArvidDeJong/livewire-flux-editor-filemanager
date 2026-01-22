{{-- Basic Usage Example --}}

<div>
    <h1>Basic Editor Usage</h1>

    {{-- Simple editor with default toolbar --}}
    <flux:field>
        <flux:label>Content</flux:label>
        <x-flux-filemanager-editor wire:model="content" />
        <flux:error name="content" />
    </flux:field>
</div>
