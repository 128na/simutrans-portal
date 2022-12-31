<?php

declare(strict_types=1);

namespace Database\Seeders\Updates;

use App\Constants\ControllOptionKeys;
use App\Models\ControllOption;
use Illuminate\Database\Seeder;

class AddControllOptionsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ControllOptionKeys::LOGIN,
            ControllOptionKeys::REGISTER,
            ControllOptionKeys::INVITATION_CODE,
            ControllOptionKeys::ARTICLE_UPDATE,
            ControllOptionKeys::TAG_UPDATE,
        ];

        foreach ($data as $key) {
            ControllOption::updateOrCreate(['key' => $key], ['value' => true]);
        }
    }
}
