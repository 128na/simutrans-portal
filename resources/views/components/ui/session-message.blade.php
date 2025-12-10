@if(request('updated'))
<div class="mx-auto @yield('max-w', 'v2-page-sm') p-6 lg:px-8">
    <p class="v2-card v2-card-primary">
        @lang('更新しました')
    </p>
</div>
<script>
    history.replaceState({}, '', location.pathname);

</script>
@endsession
@session('status')
<div class="mx-auto @yield('max-w', 'v2-page-sm') p-6 lg:px-8">
    <p class="v2-card v2-card-primary">
        @lang(session('status'))
    </p>
</div>
@endsession
@session('success')
<div class="mx-auto @yield('max-w', 'v2-page-sm') p-6 lg:px-8">
    <p class="v2-card v2-card-success">
        @lang(session('success'))
    </p>
</div>
@endsession
@session('error')
<div class="mx-auto @yield('max-w', 'v2-page-sm') p-6 lg:px-8">
    <p class="v2-card v2-card-danger">
        @lang(session('error'))
    </p>
</div>
@endsession
