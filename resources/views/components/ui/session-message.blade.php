@if(request('updated'))
<div class="mx-auto @yield('max-w', 'max-w-xl') p-6 lg:px-8">
    <p class="p-4 text-sm text-sky-900 rounded-lg bg-sky-50 border border-sky-300 ">
        @lang('更新しました')
    </p>
</div>
<script>
    history.replaceState({}, '', location.pathname);

</script>
@endsession
@session('status')
<div class="mx-auto @yield('max-w', 'max-w-xl') p-6 lg:px-8">
    <p class="p-4 text-sm text-sky-900 rounded-lg bg-sky-50 border border-sky-300 ">
        @lang(session('status'))
    </p>
</div>
@endsession
@session('success')
<div class="mx-auto @yield('max-w', 'max-w-xl') p-6 lg:px-8">
    <p class="p-4 text-sm text-green-900 rounded-lg bg-green-50 border border-green-300 ">
        @lang(session('success'))
    </p>
</div>
@endsession
@session('error')
<div class="mx-auto @yield('max-w', 'max-w-xl') p-6 lg:px-8">
    <p class="p-4 text-sm text-red-900 rounded-lg bg-red-50 border border-danger-light ">
        @lang(session('error'))
    </p>
</div>
@endsession
