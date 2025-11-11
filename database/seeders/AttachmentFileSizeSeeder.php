<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Attachment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachmentFileSizeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Attachment::chunkById(200, function ($attachments): void {
            $updates = [];

            foreach ($attachments as $attachment) {
                $path = $attachment->fullPath;
                if (!is_file($path)) {
                    continue;
                }

                $updates[] = [
                    'id' => $attachment->id,
                    'size' => filesize($path),
                ];
            }

            foreach ($updates as $update) {
                Attachment::where('id', $update['id'])->update(['size' => $update['size']]);
            }
        });
    }
}
