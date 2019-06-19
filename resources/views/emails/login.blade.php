{{ $user->name }} on {{ now() }} has logged in.<br>
<br>
== login information == <br>
IP address: {{ $_SERVER['REMOTE_ADDR'] }}<br>
Referrer: {{ $_SERVER['HTTP_REFERER'] }}<br>
User Agent: {{ $_SERVER['HTTP_USER_AGENT'] }}<br>
