{{-- Google Tag Manager --}}
<script async src="https://www.googletagmanager.com/gtag/js"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', '{{ config('app.gtag') }}', {
        debug_mode: {{ \App::environment('production') ? 'false' : 'true' }}
    });

    @isset($gtag)
        gtag('config', '{{ $gtag }}');
    @endisset
</script>
