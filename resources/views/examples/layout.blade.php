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
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:brand href="{{ route('flux-filemanager.editor-demo') }}" logo="https://fluxui.dev/img/demo/logo.png"
            name="Flux Filemanager" class="max-lg:hidden dark:hidden" />
        <flux:brand href="{{ route('flux-filemanager.editor-demo') }}"
            logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Flux Filemanager"
            class="max-lg:hidden! hidden dark:flex" />

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item icon="pencil-square" href="{{ route('flux-filemanager.editor-demo') }}"
                :current="request()->routeIs('flux-filemanager.editor-demo')">
                Editor Demo
            </flux:navbar.item>
            <flux:navbar.item icon="list-bullet" href="{{ route('flux-filemanager.filemanager-checklist') }}"
                :current="request()->routeIs('flux-filemanager.filemanager-checklist')">
                Checklist
            </flux:navbar.item>
            @auth
                <flux:navbar.item icon="arrow-right-start-on-rectangle" href="#"
                    onclick="event.preventDefault(); document.getElementById('flux-filemanager-navbar-logout-form').submit();">
                    Logout
                </flux:navbar.item>
            @endauth
        </flux:navbar>

        <flux:spacer />

        @auth
            <form id="flux-filemanager-navbar-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>
        @endauth
    </flux:header>

    <flux:sidebar sticky collapsible="mobile"
        class="border-r border-zinc-200 bg-zinc-50 lg:hidden dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <flux:sidebar.brand href="{{ route('flux-filemanager.editor-demo') }}"
                logo="https://fluxui.dev/img/demo/logo.png" logo:dark="https://fluxui.dev/img/demo/dark-mode-logo.png"
                name="Flux Filemanager" />

            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.item icon="pencil-square" href="{{ route('flux-filemanager.editor-demo') }}"
                :current="request()->routeIs('flux-filemanager.editor-demo')">
                Editor Demo
            </flux:sidebar.item>
            <flux:sidebar.item icon="list-bullet" href="{{ route('flux-filemanager.filemanager-checklist') }}"
                :current="request()->routeIs('flux-filemanager.filemanager-checklist')">
                Checklist
            </flux:sidebar.item>

            @auth
                <flux:sidebar.item icon="arrow-right-start-on-rectangle" href="#"
                    onclick="event.preventDefault(); document.getElementById('flux-filemanager-sidebar-logout-form').submit();">
                    Logout
                </flux:sidebar.item>

                <form id="flux-filemanager-sidebar-logout-form" method="POST" action="{{ route('logout') }}"
                    class="hidden">
                    @csrf
                </form>
            @endauth
        </flux:sidebar.nav>
    </flux:sidebar>

    <flux:main container>
        {{ $slot }}
    </flux:main>

    @fluxScripts

</body>

</html>
