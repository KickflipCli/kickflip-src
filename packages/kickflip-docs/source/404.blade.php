@extends('layouts.master')

@section('body')
<div class="flex flex-col items-center mt-32 text-gray-700">
    <h1 class="text-6xl font-light leading-none mb-2">404</h1>

    <h2 class="text-3xl">Page not found</h2>

    <hr class="block w-full max-w-lg mx-auto my-8 border">

    <p class="text-xl">
        Let's get you back on track, head to the <a href="{{ $site->baseUrl }}">Home Page</a>
    </p>
</div>
@endsection
