<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('user.profile.avatar-image') }} </label>
    <div class="mb-2">
        <img id="avatar-preview" class="preview img-thumbnail " src="{{ $user->profile->avatar_url ?? asset('storage/'.config('attachment.no-avatar')) }}">
    </div>
    <div class="custom-file">
        <label class="custom-file-label" for="avatar">{{ old('avatar', $user->avatar->original_name ?? __('message.not-selected')) }}</label>
        <input type="file" class="custom-file-input js-preview-trigger" id="avatar" name="avatar" data-preview="#avatar-preview">
    </div>
</div>

<div class="form-group">
    <label for="name"><span class="badge badge-danger mr-1">{{__('message.required') }}</span>{{ __('user.name') }}</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ old('name', $user->name ?? '') }}">
</div>

<div class="form-group">
    <label for="email"><span class="badge badge-danger mr-1">{{__('message.required') }}</span>{{ __('user.email') }}</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email', $user->email ?? '') }}">
</div>

<div class="form-group">
    <label for="description"><span class="badge badge-secondary mr-1">{{ __('message.optional') }}</span>{{ __('user.profile.description') }}</label>
    <textarea class="form-control" id="description" name="description" rows="4">{!! e(old('description', $user->profile->getContents('description') ?? '')) !!}</textarea>
</div>

<div class="form-group">
    <label for="website"><span class="badge badge-secondary mr-1">{{__('message.optional') }}</span>{{ __('user.profile.website') }}</label>
    <input type="text" class="form-control" id="website" name="website" placeholder="Website" value="{{ old('website', $user->profile->getContents('website') ?? '') }}">
</div>

<div class="form-group">
    <label for="twitter"><span class="badge badge-secondary mr-1">{{__('message.optional') }}</span>{{ __('user.profile.twitter') }}</label>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text">@</span>
        </div>
        <input type="text" class="form-control" id="twitter" name="twitter" placeholder="Twitter" value="{{ old('twitter', $user->profile->getContents('twitter') ?? '') }}">
    </div>
</div>
