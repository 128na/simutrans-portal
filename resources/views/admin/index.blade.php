@extends('layouts.admin')

@section('content')
<div class="container">

    <h3>キャッシュ</h3>
    <p>
        <form action="{{ route('admin.flush.cache') }}" method="POST">
            @csrf
            <button class="btn btn-danger">@lang('Clear Cache')</button>
        </form>
    </p>

</div>
@endsection
