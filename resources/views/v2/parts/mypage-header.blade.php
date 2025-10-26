<!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
<!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
<header class="bg-white">
    <nav aria-label="Global" class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8 border-b border-gray-200">
        <div class="flex lg:flex-1">
            <a href="/" class="-m-1.5 p-1.5">
                <span class="sr-only">{{config('app.name')}}</span>
                <img src="{{asset('v2/logo.svg')}}" alt="" class="h-8 w-auto" />
            </a>
        </div>
        <div class="flex lg:hidden">
            <button type="button" command="show-modal" commandfor="mobile-menu" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                <span class="sr-only">Open main menu</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                    <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
        <el-popover-group class="hidden lg:flex lg:gap-x-12">
            <a href="{{route('index')}}" class="text-sm/6 font-semibold text-gray-900">トップページ</a>
            <a href="{{route('mypage.index')}}" class="text-sm/6 font-semibold text-gray-900">マイページ</a>
            <div class="relative">
                <button popovertarget="desktop-menu-article" class="flex items-center gap-x-1 text-sm/6 font-semibold text-gray-900 cursor-pointer">
                    記事
                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none text-gray-400">
                        <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                    </svg>
                </button>

                <el-popover id="desktop-menu-article" anchor="bottom" popover class="w-screen max-w-md overflow-hidden rounded-3xl bg-white shadow-lg outline-1 outline-gray-900/5 transition transition-discrete [--anchor-gap:--spacing(3)] backdrop:bg-transparent open:block data-closed:translate-y-1 data-closed:opacity-0 data-enter:duration-200 data-enter:ease-out data-leave:duration-150 data-leave:ease-in">
                    <div class="p-4">
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('mypage.articles.index')}}" class="block font-semibold text-gray-900">
                                    一覧
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('mypage.articles.create')}}" class="block font-semibold text-gray-900">
                                    作成
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('mypage.analytics')}}" class="block font-semibold text-gray-900">
                                    アクセス解析
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('mypage.redirects')}}" class="block font-semibold text-gray-900">
                                    記事のリダイレクト設定
                                </a>
                            </div>
                        </div>
                    </div>
                </el-popover>
            </div>
            <div class="relative">
                <button popovertarget="desktop-menu-user" class="flex items-center gap-x-1 text-sm/6 font-semibold text-gray-900 cursor-pointer">
                    ユーザー
                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none text-gray-400">
                        <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                    </svg>
                </button>

                <el-popover id="desktop-menu-user" anchor="bottom" popover class="w-screen max-w-md overflow-hidden rounded-3xl bg-white shadow-lg outline-1 outline-gray-900/5 transition transition-discrete [--anchor-gap:--spacing(3)] backdrop:bg-transparent open:block data-closed:translate-y-1 data-closed:opacity-0 data-enter:duration-200 data-enter:ease-out data-leave:duration-150 data-leave:ease-in">
                    <div class="p-4">
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('mypage.profile')}}" class="block font-semibold text-gray-900">
                                    プロフィール
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('mypage.invite')}}" class="block font-semibold text-gray-900">
                                    ユーザー招待
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('mypage.login-histories')}}" class="block font-semibold text-gray-900">
                                    ログイン履歴
                                </a>
                            </div>
                        </div>
                        <div class="group relative flex items-center gap-x-6 rounded-lg p-4 text-sm/6 hover:bg-gray-50">
                            <div class="flex-auto">
                                <a href="{{route('mypage.two-factor')}}" class="block font-semibold text-gray-900">
                                    二要素認証
                                </a>
                            </div>
                        </div>
                    </div>
                </el-popover>
            </div>
            <a href="{{config('app.support_site_url')}}" class="text-sm/6 font-semibold text-gray-900" target="_blank" rel="noopener">サイトの使い方</a>

        </el-popover-group>
        <div class="hidden lg:flex lg:flex-1 lg:justify-end">
            <form action="{{route('logout')}}" method="POST">
                @csrf
                <button type="submit" class="text-sm/6 font-semibold text-gray-900 cursor-pointer">ログアウト</button>
            </form>
        </div>
    </nav>
    <el-dialog>
        <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
            <div tabindex="0" class="fixed inset-0 focus:outline-none">
                <el-dialog-panel class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white p-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                    <div class="flex items-center justify-between">
                        <a href="#" class="-m-1.5 p-1.5">
                            <span class="sr-only">{{config('app.name')}}</span>
                            <img src="{{asset('v2/logo.svg')}}" alt="" class="h-8 w-auto" />
                        </a>
                        <button type="button" command="close" commandfor="mobile-menu" class="-m-2.5 rounded-md p-2.5 text-gray-700">
                            <span class="sr-only">Close menu</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-gray-500/10">
                            <div class="space-y-2 py-6">
                                <a href="{{route('index')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">トップページ</a>
                                <a href="{{route('mypage.index')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">マイページ</a>
                                <a href="{{route('mypage.articles.index')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">一覧</a>
                                <a href="{{route('mypage.articles.create')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">作成</a>
                                <a href="{{route('mypage.analytics')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">アクセス解析</a>
                                <a href="{{route('mypage.redirects')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">記事のリダイレクト設定</a>
                                <a href="{{route('mypage.profile')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">プロフィール</a>
                                <a href="{{route('mypage.invite')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">ユーザー招待</a>
                                <a href="{{route('mypage.login-histories')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">ログイン履歴</a>
                                <a href="{{route('mypage.two-factor')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">二要素認証</a>
                                <a href="{{config('app.support_site_url')}}" class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50" target="_blank" rel="noopener">サイトの使い方</a>
                            </div>
                            <div class="py-6">
                                <form action="{{route('logout')}}" method="POST">
                                    @csrf
                                    <button type="submit" class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50 cursor-pointer">ログアウト</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
</header>
