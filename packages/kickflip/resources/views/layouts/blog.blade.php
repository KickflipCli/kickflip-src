@extends('layouts.master')

@section('body')
    <main role="main" class="w-full flex-auto prose mx-auto mb-2">
        <header class="bg-slate-500 p-2">
            <h1 class="text-lg text-white m-0">Blog Example</h1>
        </header>
        <article>
            @yield('content')
        </article>
        <section>
            {{ $page->getPreviousNextPaginator()->links() }}
        </section>
    </main>
@endsection
