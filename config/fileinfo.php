<?php

declare(strict_types=1);

return [
    'zip' => [
        // Metadata extraction can use lower budgets, but cannot exceed these ceilings.
        'max_entry_bytes' => 64 * 1024 * 1024,
        'max_total_bytes' => 256 * 1024 * 1024,
        'max_entries' => 10000,
    ],
];
