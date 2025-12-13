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
        foreach (ControllOptionKey::cases() as $option) {
            ControllOption::updateOrCreate(['key' => $option->value], ['value' => true]);
        }
    }
}
