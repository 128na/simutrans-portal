<?php

declare(strict_types=1);

use App\Services\Discord\LogConverter;

return [

    /*
     * The author of the log messages. You can set both to null to keep the Webhook author set in Discord
     */
    'from' => [
        'name' => sprintf('[%s] %s', env('APP_ENV'), env('APP_NAME')),
        'avatar_url' => null,
    ],

    /**
     * The converter to use to turn a log record into a discord message
     *
     * Bundled converters:
     * - \MarvinLabs\DiscordLogger\Converters\SimpleRecordConverter::class
     * - \MarvinLabs\DiscordLogger\Converters\RichRecordConverter::class
     */
    'converter' => LogConverter::class,

    /**
     * If enabled, stacktraces will be attached as files. If not, stacktraces will be directly printed out in the
     * message.
     *
     * Valid values are:
     *
     * - 'smart': when stacktrace is less than 2000 characters, it is inlined with the message, else attached as file
     * - 'file': stacktrace is always attached as file
     * - 'inline': stacktrace is always inlined with the message, truncated if necessary
     */
    'stacktrace' => 'smart',

    /*
     * A set of colors to associate to the different log levels when using the `RichRecordConverter`
     */
    'colors' => [
        'DEBUG' => 0x607D8B,
        'INFO' => 0x4CAF50,
        'NOTICE' => 0x2196F3,
        'WARNING' => 0xFF9800,
        'ERROR' => 0xF44336,
        'CRITICAL' => 0xE91E63,
        'ALERT' => 0x673AB7,
        'EMERGENCY' => 0x9C27B0,
    ],

    /*
     * A set of emojis to associate to the different log levels. Set to null to disable an emoji for a given level
     */
    'emojis' => [
        'DEBUG' => ':eyes:',
        'INFO' => ':information_source:',
        'NOTICE' => ':wink:',
        'WARNING' => ':warning:',
        'ERROR' => ':boom:',
        'CRITICAL' => ':innocent:',
        'ALERT' => ':japanese_ogre:',
        'EMERGENCY' => ':skull:',
    ],
];
