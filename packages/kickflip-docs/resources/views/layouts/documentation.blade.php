@extends('layouts.master')

@section('nav-toggle')
    @include('nav.menu-toggle')
@endsection

@section('body')
    <section class="container max-w-8xl mx-auto px-6 md:px-8 py-4">
        <div class="flex flex-col lg:flex-row">
            <aside id="js-nav-menu" class="hidden lg:block nav-menu">
                <nav>
                    @include('nav.menu', ['items' => $site->navigation])
                </nav>
            </aside>

            <article class="DocSearch-content w-full break-words pb-16 lg:pl-4 prose px-3 py-8 bg-white border rounded">
                <h1 id="{{ $page->getTitleId() }}">{{ $page->title }}</h1>
                @yield('docs_content')
            </article>
        </div>
    </section>
@endsection
