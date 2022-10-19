@extends('layouts.master')

@section('nav-toggle')
    @include('nav.menu-toggle')
@endsection

@section('body')
    <section class="flex flex-col lg:flex-row">
        <aside id="js-nav-menu" class="hidden lg:inline-flex nav-menu pt-4">
            <nav>
                @include('nav.menu', ['items' => $navigation])
            </nav>
        </aside>
        <article class="DocSearch-content w-full break-words mb-16 mx-auto lg:ml-4 prose px-3 sm:px-4 md:px-6 lg:px-8 py-8 bg-white border rounded-b">
            <h1 id="{{ $page->getTitleId() }}">{{ $page->title }}</h1>
            @yield('docs_content')
        </article>
        <aside class="flex-grow pl-4 pt-4">
            <x-page-toc heading="Page TOC"/>
        </aside>
    </section>
@endsection
