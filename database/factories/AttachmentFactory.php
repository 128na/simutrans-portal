<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
final class AttachmentFactory extends Factory
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
    #[\Override]
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'attachmentable_id' => null,
            'attachmentable_type' => null,
            'original_name' => 'test.png',
            'path' => 'default/test.png',
        ];
    }

    public function zipFile(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'attachmentable_id' => null,
            'attachmentable_type' => null,
            'original_name' => 'test.zip',
            'path' => 'default/test.zip',
        ];
    }

    public function image()
    {
        return $this->state(fn(array $attributes): array => [
            'original_name' => 'test.png',
            'path' => 'default/test.png',
        ]);
    }
}
