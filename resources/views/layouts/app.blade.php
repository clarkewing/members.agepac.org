<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <!-- We'll transition to a more privacy focused solution ASAP -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4BD3RY2NEC"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-4BD3RY2NEC');
    </script>

    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    <script src="{{ asset('vendor/kustomer/js/kustomer.js') }}" defer></script>
    <script>
        window.App = {!! json_encode([
            'signedIn' => Auth::check(),
            'user' => Auth::check()
                ? Auth::user()->toArray()
                 + ['isVerified' => Auth::user()->hasVerifiedEmail()]
                 + ['permissions' => Auth::user()->getAllPermissions()->pluck('name')]
                 + ['defaultPaymentMethod' => optional(Auth::user()->defaultPaymentMethod())->id]
                : null,
            'config' => [
                'scout' => [
                    'algolia' => Arr::except(config('scout.algolia'), ['secret']),
                ],
                'cashier' => Arr::only(config('cashier'), ['key']),
                'placekit' => Arr::only(config('services.placekit'), ['key']),
            ],
        ]) !!}
    </script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&family=Raleway:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @section('body')
    <div id="app">
        @include('layouts.navbar')

        <main class="py-4">
            @yield('content')
        </main>

        @include('layouts.footer')

        <flash message="{{ session('flash') }}"></flash>
    </div>
    @show

    @include('kustomer::kustomer')

    @stack('scripts')
</body>
</html>
