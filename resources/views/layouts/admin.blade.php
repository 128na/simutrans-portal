@extends('layouts.base')

@push('styles')
@vite('resources/js/mypage.ts')
@vite('resources/css/mypage.css')
@endpush

@section('header')
@include('components.layout.header-admin')
@endsection
