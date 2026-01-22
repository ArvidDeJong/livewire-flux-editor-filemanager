{{-- Advanced Usage Examples --}}

<div class="space-y-6">
    <h1>Advanced Editor Examples</h1>

    {{-- Minimal toolbar for simple text --}}
    <flux:field>
        <flux:label>Short Description</flux:label>
        <x-flux-filemanager-editor wire:model="description" toolbar="minimal" :rows="6" />
        <flux:error name="description" />
    </flux:field>

    {{-- Full toolbar for rich content --}}
    <flux:field>
        <flux:label>Article Content</flux:label>
        <x-flux-filemanager-editor wire:model="article" toolbar="full" :rows="20" />
        <flux:error name="article" />
    </flux:field>

    {{-- Custom toolbar --}}
    <flux:field>
        <flux:label>Custom Editor</flux:label>
        <x-flux-filemanager-editor wire:model="custom" :toolbar="false">
            <flux:editor.toolbar>
                <flux:editor.bold />
                <flux:editor.italic />
                <flux:editor.separator />
                <flux:editor.image />
            </flux:editor.toolbar>
        </x-flux-filemanager-editor>
        <flux:error name="custom" />
    </flux:field>
</div>
