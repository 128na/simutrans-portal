<?php

declare(strict_types=1);

return [
    /*
     * ------------------------------------------------------------------------
     * Default Firebase project
     * ------------------------------------------------------------------------
     */
    'default' => env('FIREBASE_PROJECT', 'addon-maker'),

    /*
     * ------------------------------------------------------------------------
     * Firebase project configurations
     * ------------------------------------------------------------------------
     */
    'projects' => [
        'addon-maker' => [
            'name' => 'Simutrans Addon Maker Online',
            'credentials' => [
                'file' => 'credential_addon-maker.json',
            ],
        ],
    ],
];
