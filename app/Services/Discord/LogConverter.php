<?php

declare(strict_types=1);

namespace App\Services\Discord;

use MarvinLabs\DiscordLogger\Converters\SimpleRecordConverter;
use MarvinLabs\DiscordLogger\Discord\Embed;
use MarvinLabs\DiscordLogger\Discord\Message;

class LogConverter extends SimpleRecordConverter
{
    /**
     * @param  array<mixed>  $record
     */
    protected function addMessageContent(Message $message, array $record): void
    {
        try {
            $stacktrace = $this->getStacktrace($record);
            if ($stacktrace) {
                $this->makeErrorMessage($message, $record, $stacktrace);
            } else {
                $this->makeInfoMesage($message, $record);
            }
        } catch (\Throwable $th) {
            report($th);
        }
    }

    /**
     * @param  array<mixed>  $record
     */
    private function makeErrorMessage(Message $message, array $record, string $stacktrace): void
    {
        $message
            ->content(sprintf(
                '[%s] %s: %s',
                $record['datetime']->format('Y-m-d H:i:s'),
                $record['level_name'],
                $record['message'],
            ))
            ->file($stacktrace, $this->getStacktraceFilename($record) ?? '');
    }

    /**
     * @param  array<mixed>  $record
     */
    private function makeInfoMesage(Message $message, array $record): void
    {
        $embed = Embed::make();

        $rawMessages = explode("\n", (string) $record['message']);

        $embed
            ->color($this->getRecordColor($record))
            ->title(sprintf(
                '[%s] %s: %s',
                $record['datetime']->format('Y-m-d H:i:s'),
                $record['level_name'],
                array_shift($rawMessages),
            ));

        if ($rawMessages !== []) {
            $embed->description(implode("\n", $rawMessages));
        }
        foreach ($record['context'] as $key => $value) {
            if (! is_string($value) && ! is_numeric($value)) {
                $value = json_encode($value);
            }
            $embed->field((string) $key, (string) $value);
        }

        $message->embed($embed);
    }
}
