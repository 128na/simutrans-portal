<div class="card d-flex flex-row mb-4 mr-auto profile-card">
    <div class="my-auto img-card-box">
        <img src="{{ $user->profile->avatar_url }}" class="img-card">
    </div>
    <div class="card-body">
        <h4 class="mb-1">{{ $user->name }}</h4>
        <div class="card-text card-description">{{ $user->profile->data->description ?: __('No description exists.') }}</div>
        @if ($user->profile->data->twitter)
            <div class="card-text">
                <a href="https://twitter.com/{{ $user->profile->data->twitter }}"} target="_blank" rel="noopener, nofollow">
                    &#064;{{ $user->profile->data->twitter }}</a></div>
        @endif
        @if ($user->profile->data->website)
            <div class="card-text">
                <a href="{{ $user->profile->data->website }}"} target="_blank" rel="noopener, nofollow">{{ $user->profile->data->website }}</a></div>
        @endif
    </div>
</div>
