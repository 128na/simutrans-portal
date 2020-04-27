<?php
$root = dirname(__DIR__) . '/..';
require $root . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable($root)->load();

defaults('ADMIN_USERNAME', getenv('ADMIN_EMAIL'));
defaults('ADMIN_PASSWORD', getenv('ADMIN_PASSWORD'));
