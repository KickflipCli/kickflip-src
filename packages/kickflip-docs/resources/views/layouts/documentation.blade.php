@extends('layouts.master')

@section('nav-toggle')
    @include('nav.menu-toggle')
@endsection

@section('body')
    <section class="flex flex-col lg:flex-row">
        <aside id="js-nav-menu" class="hidden lg:inline-flex nav-menu">
            <nav>
                @include('nav.menu', ['items' => $navigation])
            </nav>
        </aside>
        <article class="DocSearch-content w-full break-words mb-16 mx-auto lg:ml-4 prose px-3 sm:px-4 md:px-6 lg:px-8 py-8 bg-white border rounded">
            <h1 id="{{ $page->getTitleId() }}">{{ $page->title }}</h1>
            @yield('docs_content')
        </article>
        <aside class="flex-grow pl-4">
            <h2 class="text-lg font-thin">Page TOC</h2>
            <x-page-toc />
        </aside>
    </section>
@endsection
