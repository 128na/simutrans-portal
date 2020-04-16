@extends('layouts.admin')

@section('content')
    <h3>キャッシュ</h3>
    <p>
        <form action="{{ route('admin.flush.cache') }}" method="POST">
            @csrf
            <button class="btn btn-danger">@lang('Clear Cache')</button>
        </form>
    </p>
    <h3>デバッグ</h3>
    <p>
        <a class="btn btn-danger" href="{{ route('admin.error') }} ">Error</a>
        <a class="btn btn-danger" href="{{ route('admin.warning') }} ">Warning</a>
        <a class="btn btn-danger" href="{{ route('admin.notice') }} ">Notice</a>
    </p>
@endsection
