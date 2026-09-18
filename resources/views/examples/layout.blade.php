{{-- Demo layout. Uses only components that exist in Flux 2.0, the lowest version the package supports. --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Flux Filemanager' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen antialiased">

    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:heading size="lg" class="mr-8">Flux Filemanager</flux:heading>

        <flux:navbar class="-mb-px">
            <flux:navbar.item icon="pencil-square" href="{{ route('flux-filemanager.editor-demo') }}"
                :current="request()->routeIs('flux-filemanager.editor-demo')">
                Editor demo
            </flux:navbar.item>
            <flux:navbar.item icon="list-bullet" href="{{ route('flux-filemanager.filemanager-checklist') }}"
                :current="request()->routeIs('flux-filemanager.filemanager-checklist')">
                Checklist
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button type="submit" variant="ghost" size="sm" icon="arrow-right-start-on-rectangle">Logout</flux:button>
            </form>
        @endauth
    </flux:header>

    <flux:main container>
        {{ $slot }}
    </flux:main>

    @fluxScripts

</body>

</html>
