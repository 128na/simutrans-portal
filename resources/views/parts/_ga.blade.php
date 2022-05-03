{{-- Google Tag Manager --}}
<script async src="https://www.googletagmanager.com/gtag/js"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', '{{ config('app.gtag') }}');

    @isset($gtag)
        gtag('config', '{{ $gtag }}');
    @endisset
</script>
