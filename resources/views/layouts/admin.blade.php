@extends('layouts.base')

@push('styles')
@vite('resources/js/admin.ts')
@vite('resources/css/admin.css')
@endpush

@section('header')
@include('components.layout.header-admin')
@endsection
