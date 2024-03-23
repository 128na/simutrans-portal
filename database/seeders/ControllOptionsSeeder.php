<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ControllOptionKey;
use App\Models\ControllOption;
use Illuminate\Database\Seeder;

class ControllOptionsSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ControllOptionKey::Login,
            ControllOptionKey::Register,
            ControllOptionKey::InvitationCode,
            ControllOptionKey::ArticleUpdate,
            ControllOptionKey::TagUpdate,
            ControllOptionKey::ScreenshotUpdate,
        ];

        foreach ($data as $key) {
            ControllOption::updateOrCreate(['key' => $key], ['value' => true]);
        }
    }
}
