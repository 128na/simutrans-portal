<div class="form-group">
    <label><span class="badge badge-secondary mr-1">{{ __('Optional') }}</span>{{ __('Avatar image') }} </label>
    <div class="mb-2">
        <img id="avatar_preview" class="preview img-thumbnail " src="{{ old('avatar_preview_url', $user->profile->avatar_url ?? asset('storage/'.config('attachment.no-avatar'))) }}">
        <input type="hidden" id="avatar_preview_url" name="avatar_preview_url" value="{{ old('avatar_preview_url') }}">
        <input type="hidden" id="avatar_id" name="avatar_id" value="{{ old('avatar_id', $user->profile->getContents('avatar') ?? '') }}">
    </div>
    <div>
        <a href="#" class="btn btn-secondary js-open-uploader"
            data-input="#avatar_id" data-preview="#avatar_preview" data-preview-url="#avatar_preview_url" data-only-image="true"
        >{{ __('Open File Manager') }}</a>
    </div>
</div>

<div class="form-group">
    <label for="name"><span class="badge badge-danger mr-1">{{__('Required') }}</span>{{ __('Name') }}</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ old('name', $user->name ?? '') }}">
</div>

<div class="form-group">
    <label for="email"><span class="badge badge-danger mr-1">{{__('Required') }}</span>{{ __('Email') }}</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email', $user->email ?? '') }}">
</div>

<div class="form-group">
    <label for="description"><span class="badge badge-secondary mr-1">{{ __('Optional') }}</span>{{ __('Description') }}</label>
    <textarea class="form-control" id="description" name="description" rows="4">{!! e(old('description', $user->profile->getContents('description') ?? '')) !!}</textarea>
</div>

<div class="form-group">
    <label for="website"><span class="badge badge-secondary mr-1">{{__('Optional') }}</span>{{ __('Website URL') }}</label>
    <input type="text" class="form-control" id="website" name="website" placeholder="Website" value="{{ old('website', $user->profile->getContents('website') ?? '') }}">
</div>

<div class="form-group">
    <label for="twitter"><span class="badge badge-secondary mr-1">{{__('Optional') }}</span>{{ __('Twitter ID') }}</label>
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text">@</span>
        </div>
        <input type="text" class="form-control" id="twitter" name="twitter" placeholder="Twitter" value="{{ old('twitter', $user->profile->getContents('twitter') ?? '') }}">
    </div>
</div>
