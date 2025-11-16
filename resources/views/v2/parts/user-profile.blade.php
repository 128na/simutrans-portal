<div class="flex items-center gap-x-3">
    <img src="{{$user->profile->avatarUrl}}" alt="user avatar" class="w-10 h-10 rounded-full bg-gray-50" />
    <div class="text-sm">
        <p class="font-semibold text-gray-900 break-all">
            <a href="{{ route('search', ['userIds' => [$user->id]]) }}">
                {{$user->name}}
            </a>
        </p>
        <p class="text-gray-600 break-all">{{$user->profile->data->description}}</p>
        @foreach ($user->profile->data->website as $website)
        @dump($website)
        @includeIf('v2.parts.link-external', ['url' => $website])
        @endforeach
    </div>
</div>
