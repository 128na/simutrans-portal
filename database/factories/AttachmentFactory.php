<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'attachmentable_id' => null,
            'attachmentable_type' => null,
            'original_name' => 'test.png',
            'path' => 'test.png',
        ];
    }

    public function zipFile()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'attachmentable_id' => null,
            'attachmentable_type' => null,
            'original_name' => 'test.zip',
            'path' => 'test.zip',
        ];
    }
}
