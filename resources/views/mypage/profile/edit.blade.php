@extends('layouts.mypage')

@section('title', __('message.edit-profile'))

@section('content')
    <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @include('mypage.profile._form')
        @include('parts._modal_uploader')

        <div class="form-group">
            <button class="btn btn-lg btn-primary">{{ __('message.save') }}</button>
        </div>
    </form>
@endsection
