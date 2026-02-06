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
                        <a href="{{ route('login') }}" class="v2-header-menu-item">ログイン <span aria-hidden="true">&rarr;</span></a>
                    @endauth
                </div>
            </div>
        </div>
        <div class="hidden lg:flex items-center justify-between pt-4">
            <el-popover-group class="flex gap-x-4">
            <a href="{{route('pak.128japan')}}" class="v2-header-menu-item">128.japan</a>
            <a href="{{route('pak.128')}}" class="v2-header-menu-item">128</a>
            <a href="{{route('pak.64')}}" class="v2-header-menu-item">64</a>
            <a href="{{route('users.index')}}" class="v2-header-menu-item">ユーザー</a>
            <a href="{{route('categories.index')}}" class="v2-header-menu-item">カテゴリ</a>
            <a href="{{route('tags.index')}}" class="v2-header-menu-item">タグ</a>

            <div class="relative">
                <button popovertarget="desktop-menu-misc" class="flex items-center gap-x-1 v2-header-menu-item cursor-pointer">
                    その他
                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none text-c-sub">
                        <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                    </svg>
                </button>

                <el-popover id="desktop-menu-misc" anchor="bottom" popover class="w-screen max-w-md overflow-hidden rounded-3xl shadow-lg outline-1 outline-c-main/5 transition transition-discrete [--anchor-gap:--spacing(3)] backdrop:bg-transparent open:block data-closed:translate-y-1 data-closed:opacity-0 data-enter:duration-200 data-enter:ease-out data-leave:duration-150 data-leave:ease-in">
                    <div class="p-4">
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('pak.others')}}" class="block font-semibold">
                                    他pak
                                    <span class="absolute inset-0"></span>
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('pages')}}" class="block font-semibold">
                                    一般記事
                                    <span class="absolute inset-0"></span>
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('search')}}" class="block font-semibold">
                                    詳細検索
                                    <span class="absolute inset-0"></span>
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{ route('social') }}" class="block font-semibold">
                                    SNS・通知ツール
                                    <span class="absolute inset-0"></span>
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{config('app.support_site_url')}}" class="block font-semibold" target="_blank" rel="noopener">
                                    サイトの使い方<span class="text-sm text-c-sub/50">↗</span>
                                    <span class="absolute inset-0"></span>
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{config('app.privacy_policy_url')}}" class="block font-semibold" target="_blank" rel="noopener">
                                    プライバシーポリシー<span class="text-sm text-c-sub/50">↗</span>
                                    <span class="absolute inset-0"></span>
                                </a>
                            </div>
                        </div>

                    </div>
                </el-popover>
            </div>

            </el-popover-group>
            <form method="GET" action="{{ route('search') }}" class="flex-1">
                <div class="flex items-center justify-end gap-x-2">
                    <input type="search" name="word" placeholder="キーワードを入力" class="flex-1 v2-input max-w-xs" />
                    <button type="submit" class="v2-button v2-button-md v2-button-primary">検索</button>
                </div>
            </form>
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
                                <a href="{{route('pak.128japan')}}" class="v2-header-menu-item-sp">128.japan</a>
                                <a href="{{route('pak.128')}}" class="v2-header-menu-item-sp">128</a>
                                <a href="{{route('pak.64')}}" class="v2-header-menu-item-sp">64</a>
                                <a href="{{route('pak.others')}}" class="v2-header-menu-item-sp">他pak</a>
                                <a href="{{route('users.index')}}" class="v2-header-menu-item-sp">ユーザー一覧</a>
                                <a href="{{route('categories.index')}}" class="v2-header-menu-item-sp">カテゴリ一覧</a>
                                <a href="{{route('tags.index')}}" class="v2-header-menu-item-sp">タグ一覧</a>
                                <a href="{{route('pages')}}" class="v2-header-menu-item-sp">一般記事</a>
                                <a href="{{route('search')}}" class="v2-header-menu-item-sp">詳細検索</a>
                                <a href="{{ route('social') }}" class="v2-header-menu-item-sp">SNS・通知ツール</a>
                                <a href="{{config('app.support_site_url')}}" class="v2-header-menu-item-sp" target="_blank" rel="noopener">サイトの使い方<span class="text-sm text-c-sub/50">↗</span></a>
                                <a href="{{config('app.privacy_policy_url')}}" class="v2-header-menu-item-sp" target="_blank" rel="noopener">プライバシーポリシー<span class="text-sm text-c-sub/50">↗</span></a>
                            </div>
                            <div class="py-6">
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
