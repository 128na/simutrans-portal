<?php

namespace Database\Seeders;

use App\Models\Attachment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachmentFileSizeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Attachment::chunkById(200, function ($attachments) {
            $updates = [];

            foreach ($attachments as $attachment) {
                $path = $attachment->fullPath;
                if (!is_file($path)) continue;

                $updates[] = [
                    'id' => $attachment->id,
                    'size' => filesize($path),
                ];
            }

            foreach ($updates as $row) {
                Attachment::where('id', $row['id'])->update(['size' => $row['size']]);
            }
        });
    }
}
