<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full bg-white motion-safe:scroll-smooth"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @if($title)
                {{ $title }} –
            @endif
            {{ config('app.name', 'Laravel') }}
        </title>

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#4338ca">
        <meta name="apple-mobile-web-app-title" content="PixAir Survey">
        <meta name="application-name" content="PixAir Survey">
        <meta name="msapplication-TileColor" content="#4338ca">
        <meta name="theme-color" content="#ffffff">
        <!-- / Favicon -->

        <!-- Fonts -->
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <!-- / Fonts -->

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        @livewireStyles
        <!-- / Styles -->

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
        <!-- / Scripts -->
    </head>

    <body {{ $attributes->merge(['class' => 'font-sans antialiased']) }}>
        {{ $slot }}

        <!-- Scripts -->
        @livewireScripts
{{--        @livewire('livewire-ui-modal')--}}
{{--        @livewireUIScripts--}}
        @stack('scripts')
        <!-- / Scripts -->
    </body>
</html>
