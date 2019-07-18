<div class="card d-flex flex-row mb-4 mr-auto profile-card">
    <a href="{{ route('mypage.profile.edit') }}" class="my-auto img-card-box">
        <img src="{{ $user->profile->avatar_url }}" class="img-card">
    </a>
    <div class="card-body">
        <h4 class="mb-1">{{ $user->name }}</h4>
        <div class="card-text card-description">{{ $user->profile->getContents('description') ?: __('No description exists.') }}</div>
        @if ($user->profile->getContents('twitter'))
            <div class="card-text">
                <a href="https://twitter.com/{{ $user->profile->getContents('twitter') }}"} target="_blank" rel="noopener, nofollow">
                    &#064;{{ $user->profile->getContents('twitter') }}</a></div>
        @endif
        @if ($user->profile->getContents('website'))
            <div class="card-text">
                <a href="{{ $user->profile->getContents('website') }}"} target="_blank" rel="noopener, nofollow">{{ $user->profile->getContents('website') }}</a></div>
        @endif
        @if ($in_mypage)
            <div class="card-text mt-2 text-right">
                <a href="{{ route('mypage.profile.edit') }}">@lang('Edit my profile')</a>
            </div>
        @endif
    </div>
    @if ($in_mypage)
        @if ($user->email_verified_at)
            <small class="verify-status ml-1 mt-1 px-1 rounded border border-success text-success">@lang('Verified')</small>
        @else
            <small class="verify-status ml-1 mt-1 px-1 rounded border border-danger text-danger">@lang('NOT verified')</small>
        @endif
    @endif
</div>
