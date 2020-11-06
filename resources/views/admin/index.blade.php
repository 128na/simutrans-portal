@extends('layouts.admin')

@section('content')
    <h3>phpinfo</h3>
    <p>
        <a class="btn btn-danger" href="{{ route('admin.phpinfo') }} ">phpinfo</a>
    </p>
    <h3>キャッシュ</h3>
    <p>
        <form action="{{ route('admin.flush.cache') }}" method="POST">
            @csrf
            <button class="btn btn-danger">キャッシュ削除</button>
        </form>
    </p>
    <h3>デバッグ</h3>
    <p>
        <a class="btn btn-danger" href="{{ route('admin.error') }} ">Error</a>
        <a class="btn btn-danger" href="{{ route('admin.warning') }} ">Warning</a>
        <a class="btn btn-danger" href="{{ route('admin.notice') }} ">Notice</a>
    </p>
@endsection
