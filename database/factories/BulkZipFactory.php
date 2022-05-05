<?php

namespace Database\Factories;

use App\Models\BulkZip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BulkZipFactory extends Factory
{
    protected $model = BulkZip::class;

    public function definition()
    {
        $type = $this->faker->randomElement([User::class]);

        return [
            'bulk_zippable_id' => $type::factory()->create()->id,
            'bulk_zippable_type' => $type,
            'generated' => false,
            'path' => null,
        ];
    }
}
