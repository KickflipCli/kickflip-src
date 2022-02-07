@extends('layouts.master')

@section('body')
    <header class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:text-neutral-50 dark:bg-gray-900 sm:items-center py-4 sm:pt-0" role="banner">
        Hello, world!
        <x-emoji-icon::skateboard class="text-5xl" />
    </header>

    <main role="main" class="w-full flex-auto prose mx-auto mb-2">
        @yield('content')
    </main>
@endsection
