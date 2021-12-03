<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="{{ $page->description ?? $site->siteDescription }}">

        <meta property="og:site_name" content="{{ $site->siteName }}"/>
        <meta property="og:title" content="{{ isset($page->title) && ! empty($page->title) ?  $page->title . ' | ' : '' }}{{ $site->siteName }}"/>
        <meta property="og:description" content="{{ $page->description ?? $site->siteDescription }}"/>
        <meta property="og:url" content="{{ $page->getUrl() }}"/>
        <meta property="og:image" content="/assets/img/logo.png"/>
        <meta property="og:type" content="website"/>

        <meta name="twitter:image:alt" content="{{ $site->siteName }}">
        <meta name="twitter:card" content="summary_large_image">

        @if (KickflipHelper::config('docsearchApiKey') && KickflipHelper::config('docsearchIndexName'))
            <meta name="generator" content="kickflip_kickflip_doc">
        @endif

        <title>{{ isset($page->title) && ! empty($page->title) ?  $page->title . ' | ' : '' }}{{ $site->siteName }}</title>

        @isset($site->baseUrl)
        <link rel="home" href="{{ $site->baseUrl }}">
        @endisset
        <link rel="icon" href="/favicon.ico">

        @stack('meta')

        @if ($site->production)
            <!-- Insert analytics code here -->
        @endif

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,300i,400,400i,700,700i,800,800i" rel="stylesheet">
        <link rel="stylesheet" href="{{ mix('css/main.css', 'assets/build') }}">

        @if (KickflipHelper::config('docsearchApiKey') && KickflipHelper::config('docsearchIndexName'))
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/docsearch.js@2/dist/cdn/docsearch.min.css" />
        @endif
    </head>

    <body class="flex flex-col justify-between min-h-screen bg-gray-100 text-gray-800 leading-normal font-sans">
        <header class="flex items-center h-24 mb-8 py-4" role="banner">
            <div class="container flex items-center max-w-8xl mx-auto px-4 lg:px-8">
                <div class="flex items-center">
                    <a href="/" title="{{ $site->siteName }} home" class="inline-flex items-center">
                        <x-emoji-icon::skateboard id="logo" class="text-5xl mr-3" alt="{{ $site->siteName }} logo" />

                        <h1 class="text-lg md:text-2xl text-blue-900 font-semibold hover:text-blue-600 my-0 pr-4">{{ $site->siteName }}</h1>
                    </a>
                </div>

                <div class="flex flex-1 justify-end items-center text-right md:pl-10">
                    <a class="mr-4 block text-sm text-red-700 hover:text-red-900 uppercase font-medium">
                        <span class="hidden md:inline-block hover:underline capitalize">Contribute</span>
                        <x-bxl-github class="md:hidden h-8 md:h-10" />
                    </a>
                    <a href="{{ getDocUrl('getting-started') }}" class="block text-sm py-2 px-4 md:py-3 md:px-4 bg-red-700 hover:bg-red-900 rounded text-white uppercase font-medium shadow-md">Docs</a>
                    @if (KickflipHelper::config('docsearchApiKey') && KickflipHelper::config('docsearchIndexName'))
                        @include('nav.search-input')
                    @endif
                </div>
            </div>

            @yield('nav-toggle')
        </header>

        <main role="main" class="w-full flex-auto">
            @yield('body')
        </main>

        <script src="{{ mix('js/main.js', 'assets/build') }}"></script>

        @stack('scripts')

        @include('layouts.footer')

    </body>
</html>
