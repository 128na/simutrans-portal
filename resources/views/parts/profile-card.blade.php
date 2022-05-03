<div class="card profile-card mt-4">
    <div>
        <img src="{{ $user->profile->avatar_url }}" class="rounded">
    </div>
    <div class="card-body">
        <h5 class="card-title">{{ $user->name }}</h5>
        <p class="card-text">
            {{ $user->profile->data->description ?: '説明がありません' }}
        </p>
        @if ($user->profile->data->twitter)
            <div><a href="https://twitter.com/{{ $user->profile->data->twitter }}" target="_blank"
                    rel="noopener nofollow">&#064;{{ $user->profile->data->twitter }}</a></div>
        @endif
        @if ($user->profile->data->website)
            <div><a href="{{ $user->profile->data->website }}" } target="_blank"
                    rel="noopener nofollow">{{ $user->profile->data->website }}</a></div>
        @endif
    </div>
</div>
