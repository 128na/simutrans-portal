@extends('layouts.base')

@push('styles')
@vite('resources/js/front.ts')
@vite('resources/css/front.css')
@endpush

@section('header')
@include('components.layout.header')
@endsection
