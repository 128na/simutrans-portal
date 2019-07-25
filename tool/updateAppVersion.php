<?php
$version = trim(fgets(STDIN));
$path = __DIR__.'/../.env';

$env = file_get_contents($path);
$new_env = preg_replace("/(APP_VERSION).*/",'$1='.$version, $env);
file_put_contents($path, $new_env);
