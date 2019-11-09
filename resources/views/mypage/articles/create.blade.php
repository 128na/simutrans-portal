@extends('layouts.mypage')

@section('title', __('Create :post_type', ['post_type' => __('post_types.'.$post_type)]))

@section('content')
    <form method="POST" action="{{ route('mypage.articles.store.'.$post_type) }}" enctype="multipart/form-data"
        class="js-previewable-form" data-preview-action="{{ route('mypage.articles.store.'.$post_type, 'preview', false) }}">
        @csrf
        @include('parts._form-common')
        @include('parts._form-'.$post_type)
        @include('parts._modal_uploader')

        <hr>
        <div class="form-group">
            <label>@lang('Auto Tweet')</label>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="should_tweet" name="should_tweet"
                    value="1" {{ old('should_tweet', true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="should_tweet">@lang('Tweet when posting or updating.')</label>
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-primary">@lang('Save')</button>
            <button class="btn btn btn-secondary js-open-preview">@lang('Preview')</button>
        </div>
    </form>
@endsection
