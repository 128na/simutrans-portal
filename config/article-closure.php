<?php

declare(strict_types=1);

return [
    'mailer' => env('ARTICLE_CLOSURE_MAILER'),
    'smtp_timeout' => 30,
    'max_attempts' => 3,
    'retry_delay' => 60,
    'run_timeout' => 300,
    'sending_timeout' => 120,
    'recovery_limit' => 100,
    'dispatch_lease' => 900,
];
