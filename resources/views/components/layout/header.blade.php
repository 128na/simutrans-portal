<!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
<!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
<header class="bg-white">
    <nav aria-label="Global" class="mx-auto v2-page-lg p-6 lg:px-8 border-b border-c-sub/10">
        <div class="flex items-center justify-between gap-x-4">
            <div class="flex lg:flex-1">
                <a href="/" class="-m-1.5 p-1.5">
                    <span class="sr-only">{{config('app.name')}}</span>
                    <img src="{{asset('v2/logo.svg')}}" alt="" class="h-8 w-auto" />
                </a>
            </div>
            <el-popover-group class="hidden lg:flex gap-x-4">
                <form method="GET" action="{{ route('search') }}" class="hidden lg:flex items-center gap-x-2">
                    <input type="search" name="word" placeholder="キーワードを入力" class="v2-input w-64" />
                    <button type="submit" class="v2-button-outline v2-button-primary-outline v2-button-md">検索</button>
                </form>
                <a href="{{route('latest')}}" class="v2-header-menu-item">新着アドオン</a>
            </el-popover-group>
            <div class="flex items-center gap-x-4">
                <div class="flex lg:hidden">
                    <button type="button" command="show-modal" commandfor="mobile-menu" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-c-sub">
                        <span class="sr-only">Open main menu</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <div class="hidden lg:flex lg:justify-end">
                    @auth
                        @php
                            $avatarUrl = optional(auth()->user()?->profile?->avatar)?->thumbnail
                                ?? \Illuminate\Support\Facades\Storage::disk('public')->url(\App\Constants\DefaultThumbnail::NO_AVATAR);
                        @endphp
                        <a href="{{ route('mypage.index') }}" class="v2-header-menu-item flex items-center gap-2">
                            <img class="w-6 h-6 rounded-full bg-gray-50" src="{{ $avatarUrl }}" alt="" />
                            <span>マイページ</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="v2-header-menu-item">ログイン</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <el-dialog>
        <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
            <div tabindex="0" class="fixed inset-0 focus:outline-none">
                <el-dialog-panel class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto p-6 sm:max-w-sm sm:ring-1 sm:ring-c-main/10 bg-white">
                    <div class="flex items-center justify-between">
                        <a href="#" class="-m-1.5 p-1.5">
                            <span class="sr-only">{{config('app.name')}}</span>
                            <img src="{{asset('v2/logo.svg')}}" alt="" class="h-8 w-auto" />
                        </a>
                        <button type="button" command="close" commandfor="mobile-menu" class="-m-2.5 rounded-md p-2.5 text-c-sub">
                            <span class="sr-only">Close menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-c-sub/10">
                            <div class="space-y-2 py-6">
                                <form method="GET" action="{{ route('search') }}" class="mb-6">
                                    <div class="flex items-center justify-end gap-x-2">
                                        <input type="search" name="word" placeholder="キーワードを入力" class="flex-1 v2-input" />
                                        <button type="submit" class="v2-button v2-button-md v2-button-primary">検索</button>
                                    </div>
                                </form>
                                <a href="{{route('latest')}}" class="v2-header-menu-item-sp">新着一覧</a>
                                @auth
                                    <a href="{{ route('mypage.index') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold hover:bg-gray-50">マイページ</a>
                                @else
                                    <a href="{{ route('login') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold hover:bg-gray-50">ログイン</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
</header>
