<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Screenshot;

use App\Models\Screenshot;
use Carbon\Carbon;

final readonly class GetScreenshotParam
{
    public function __construct(
        private Carbon $carbon,
    ) {}

    /**
     * @return array<string,string>
     */
    public function __invoke(Screenshot $screenshot): array
    {
        $url = route('screenshots.show', $screenshot);
        $now = $this->carbon->format('Y/m/d H:i');
        $name = $screenshot->user->name;
        $tags = collect(['simutrans'])
            ->map(fn ($slug): string => __('hash_tag.'.$slug))
            ->implode(' ');

        return ['title' => $screenshot->title, 'url' => $url, 'name' => $name, 'at' => $now, 'tags' => $tags];
    }
}
