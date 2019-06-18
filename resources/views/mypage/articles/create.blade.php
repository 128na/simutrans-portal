@extends('layouts.app')

@section('title', 'New '.$post_type)

@section('content')
    <form method="POST" action="{{ route('mypage.articles.store.'.$post_type) }}" enctype="multipart/form-data">
        @csrf
        @include('mypage.articles._form-common')
        @include('mypage.articles._form-'.$post_type)

        <div class="form-group">
            <button class="btn btn-lg btn-primary">Submit</button>
        </div>
    </form>
    <script src="{{ asset('js/form.js') }}" defer></script>
@endsection
