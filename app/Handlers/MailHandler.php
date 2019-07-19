<?php
namespace App\Handlers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Monolog\Handler\MailHandler as BaseHandler;

class MailHandler extends BaseHandler
{
    protected function send($content, array $records): void
    {
        $admins = User::admin()->get()->pluck('email')->all();

        foreach ($records as $record) {
            Mail::raw($content, function ($message) use($admins, $content, $record) {
                $message->cc($admins)->subject("{$record['message']} @{$record['channel']}");
            });
        }
    }
}
