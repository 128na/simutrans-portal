@extends('layouts.mypage')

@section('title', __('Edit my profile'))

@section('content')
    <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @include('mypage.profile._form')
        @include('parts._modal_uploader')

        <div class="form-group">
            <button class="btn btn-lg btn-primary">@lang('Save')</button>
        </div>
    </form>
@endsection
