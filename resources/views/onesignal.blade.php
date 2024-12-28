<?php

declare(strict_types=1);

?>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
<script>
    window.OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
            appId: "{{ config('onesignal.app_id') }}",
        });
    });
</script>
<?php 
