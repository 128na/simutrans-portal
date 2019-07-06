@extends('layouts.admin')

@section('content')
<div class="container">

    <h3>キャッシュ</h3>
    <p>
        <form action="{{ route('admin.flush.cache') }}" method="POST">
            @csrf
            <button class="btn btn-danger">キャッシュ削除</button>
        </form>
    </p>

</div>
@endsection
