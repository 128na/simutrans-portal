@extends('layouts.base')

@push('styles')
@vite('resources/js/admin.ts')
@vite('resources/css/admin.css')
@endpush

@section('header')
@include('components.layout.header-admin')
@endsection

@section('content')
<div class="mx-auto v2-page-lg">
	<div class="lg:flex lg:gap-8">
		<aside class="hidden lg:block w-64 shrink-0 border-r border-c-sub/10">
			<div class="sticky top-6 p-3 lg:py-4 lg:pr-3">
				@include('components.layout.sidebar-admin')
			</div>
		</aside>
		<main class="min-w-0 flex-1">
			@yield('page-content')
		</main>
	</div>
</div>
@endsection
