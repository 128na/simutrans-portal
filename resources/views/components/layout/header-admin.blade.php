<!-- Include this script tag or install `@tailwindplus/elements` via npm: -->
<!-- <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script> -->
<header class="bg-white">
  <nav aria-label="Global" class="v2-header">
    <div class="flex lg:flex-1">
      <a href="/" class="-m-1.5 p-1.5">
        <span class="sr-only">{{ config('app.name') }}</span>
        <img src="{{ asset('v2/logo.svg') }}" alt="" class="h-8 w-auto" />
      </a>
    </div>
    <div class="flex lg:hidden">
      <button
        type="button"
        command="show-modal"
        commandfor="mobile-menu"
        class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-c-sub"
      >
        <span class="sr-only">Open main menu</span>
        <svg
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          data-slot="icon"
          aria-hidden="true"
          class="size-6"
        >
          <path
            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
      </button>
    </div>
    <el-popover-group class="hidden lg:flex lg:gap-x-4"></el-popover-group>
    <div class="hidden lg:flex lg:flex-1 lg:justify-end">
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="v2-header-menu-item cursor-pointer">
          ログアウト
        </button>
      </form>
    </div>
  </nav>
  <el-dialog>
    <dialog id="mobile-menu" class="backdrop:bg-transparent lg:hidden">
      <div tabindex="0" class="fixed inset-0 focus:outline-none">
        <el-dialog-panel class="v2-mobile-menu">
          <div class="flex items-center justify-between">
            <a href="#" class="-m-1.5 p-1.5">
              <span class="sr-only">{{ config('app.name') }}</span>
              <img
                src="{{ asset('v2/logo.svg') }}"
                alt=""
                class="h-8 w-auto"
              />
            </a>
            <button
              type="button"
              command="close"
              commandfor="mobile-menu"
              class="-m-2.5 rounded-md p-2.5 text-c-sub"
            >
              <span class="sr-only">Close menu</span>
              <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                data-slot="icon"
                aria-hidden="true"
                class="size-6"
              >
                <path
                  d="M6 18 18 6M6 6l12 12"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </button>
          </div>
          <div class="mt-6 flow-root">
            <div class="-my-6 divide-y divide-c-sub/10">
              <div class="space-y-2 py-6">
                @include('components.layout.sidebar-admin')
              </div>
              <div class="py-6">
                <form action="{{ route('logout') }}" method="POST">
                  @csrf
                  <button
                    type="submit"
                    class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold hover:bg-gray-50 cursor-pointer"
                  >
                    ログアウト
                  </button>
                </form>
              </div>
            </div>
          </div>
        </el-dialog-panel>
      </div>
    </dialog>
  </el-dialog>
</header>
