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
        <meta property="og:url" content="{{ rtrim($site->baseUrl, '/') . '/' . ltrim($page->getUrl(), '/') }}"/>
        <meta property="og:image" content="/assets/img/logo.png"/>
        <meta property="og:type" content="website"/>

        <meta name="twitter:image:alt" content="{{ $site->siteName }}">
        <meta name="twitter:card" content="summary_large_image">

        <title>{{ isset($page->title) && ! empty($page->title) ?  $page->title . ' | ' : '' }}{{ $site->siteName }}</title>
    @isset($site->baseUrl)
        <link rel="home" href="{{ $site->baseUrl }}">
    @endisset
        <link rel="icon" href="/favicon.ico">

        @stack('meta')

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,300i,400,400i,700,700i,800,800i" rel="stylesheet">
        <link rel="stylesheet" href="{{ KickflipHelper::mix('css/main.css') }}">
    </head>

    <body class="flex flex-col justify-between min-h-screen bg-gray-100 text-gray-800 leading-normal font-sans">
        <header class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:text-neutral-50 dark:bg-gray-900 sm:items-center py-4 sm:pt-0" role="banner">
            Hello, world!
            <x-emoji-icon::skateboard class="text-5xl" />
        </header>

        <main role="main" class="w-full flex-auto prose mx-auto mb-2">
            @yield('body')
        </main>

        <script src="{{ KickflipHelper::mix('js/main.js') }}"></script>
        @stack('scripts')
        <footer class="bg-white text-center text-sm py-4 border-t" role="contentinfo">
            <ul class="flex flex-col md:flex-row justify-center">
                <li class="md:mr-2">
                    Built by <a href="https://github.com/mallardduck">MallardDuck</a> with Kickflip!
                </li>

                <li class="flex flex-col items-center">
                    <a href="https://github.com/mallardduck/kickflip" title="Kickflip on GitHub" target="_blank">
                        Contribute on GitHub
                    </a>
                </li>
            </ul>
        </footer>
    </body>
</html>
