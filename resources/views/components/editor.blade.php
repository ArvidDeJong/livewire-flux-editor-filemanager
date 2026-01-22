@props([
    'id' => null,
    'rows' => 12,
    'toolbar' => 'default', // 'default', 'minimal', 'full', or false for custom
])

<flux:editor :id="$id" {{ $attributes }} :rows="$rows">
    @if ($toolbar !== false)
        <flux:editor.toolbar>
            @if ($toolbar === 'minimal')
                {{-- Minimal toolbar: basic formatting only --}}
                <flux:editor.bold />
                <flux:editor.italic />
                <flux:editor.separator />
                <flux:editor.link />
            @elseif($toolbar === 'full')
                {{-- Full toolbar: all options --}}
                <flux:editor.heading />
                <flux:editor.separator />
                <flux:editor.bold />
                <flux:editor.italic />
                <flux:editor.strike />
                <flux:editor.underline />
                <flux:editor.separator />
                <flux:editor.bullet />
                <flux:editor.ordered />
                <flux:editor.blockquote />
                <flux:editor.separator />
                @include('flux-filemanager::flux.editor.image')
                <flux:editor.link />
                @include('flux-filemanager::flux.editor.file-link')
                <flux:editor.separator />
                <flux:editor.align />
                <flux:editor.separator />
                <flux:editor.code />
            @else
                {{-- Default toolbar: most commonly used options --}}
                <flux:editor.heading />
                <flux:editor.separator />
                <flux:editor.bold />
                <flux:editor.italic />
                <flux:editor.strike />
                <flux:editor.separator />
                <flux:editor.bullet />
                <flux:editor.ordered />
                <flux:editor.blockquote />
                <flux:editor.separator />
                @include('flux-filemanager::flux.editor.image')
                <flux:editor.link />
                @include('flux-filemanager::flux.editor.file-link')
                <flux:editor.separator />
                <flux:editor.align />
            @endif
        </flux:editor.toolbar>
    @else
        {{-- Custom toolbar via slot --}}
        {{ $slot }}
    @endif

    <flux:editor.content />
</flux:editor>
