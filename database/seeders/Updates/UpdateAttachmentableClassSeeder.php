<?php

declare(strict_types=1);

namespace Database\Seeders\Updates;

use App\Models\Attachment;
use App\Models\User\Profile;
use Illuminate\Database\Seeder;

class UpdateAttachmentableClassSeeder extends Seeder
{
    /**
     * Profileクラスの名前空間修正用.
     */
    public function run()
    {
        Attachment::where('attachmentable_type', 'App\Models\Profile')->update(['attachmentable_type' => Profile::class]);
    }
}
