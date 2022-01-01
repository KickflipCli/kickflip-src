@php
$date = "2018-02-16";
@endphp

@extends('layouts.post')

@section('postContent')
    <h1>Post Stuff</h1>
    <p>Post content stuff.</p>
    {{ $date }}
@endsection
