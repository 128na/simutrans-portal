<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Monolog\Handler\MailHandler as BaseHandler;

class MailHandler extends BaseHandler
{
    /**
     * @param  array<\Monolog\LogRecord>  $records
     */
    protected function send(string $content, array $records): void
    {
        $admins = User::select('email')->admin()->pluck('email')->all();

        foreach ($records as $record) {
            Mail::raw(
                $content,
                fn ($message) => $message->cc($admins)->subject("{$record->message} @{$record->channel}")
            );
        }
    }
}
