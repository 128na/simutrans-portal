<!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
<!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
<header class="bg-white">
  <nav
    aria-label="Global"
    class="v2-header"
  >
    <div class="flex items-center justify-between gap-x-4">
      <div class="flex lg:flex-1">
        <a href="/" class="-m-1.5 p-1.5">
          <span class="sr-only">{{ config('app.name') }}</span>
          <img src="{{ asset('v2/logo.svg') }}" alt="" class="h-8 w-auto" />
        </a>
      </div>
      <div class="flex items-center gap-x-4">
        <div class="flex justify-end">
          @guest
            <a href="{{ route('login') }}" class="v2-header-menu-item">
              ログイン
            </a>
          @endguest

          @auth
            @php
              $avatarUrl = optional(auth()->user()?->profile?->avatar)?->thumbnail
                  ?? \Illuminate\Support\Facades\Storage::disk('public')->url(\App\Constants\DefaultThumbnail::NO_AVATAR);
            @endphp

            <a
              href="{{ route('mypage.index') }}"
              class="v2-header-menu-item flex items-center gap-2"
            >
              <img
                class="w-6 h-6 rounded-full bg-gray-50"
                src="{{ $avatarUrl }}"
                alt=""
              />
              <span>マイページ</span>
            </a>
          @endauth
        </div>
      </div>
    </div>
  </nav>
</header>
