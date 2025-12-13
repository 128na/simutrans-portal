<?php

declare(strict_types=1);

namespace App\Actions\ArticleSearchIndex;

use Illuminate\Support\Facades\DB;

class UpdateOrCreateAction
{
    public function __invoke(int $articleId): void
    {
        DB::statement(<<<'SQL'
    INSERT INTO article_search_index (article_id, text, created_at, updated_at)
    SELECT
        a.id AS article_id,
        CONCAT_WS(' ', a.title, a.contents, fi.data) AS text,
        NOW(),
        NOW()
    FROM articles a
    LEFT JOIN attachments att
      ON att.attachmentable_id = a.id
     AND att.attachmentable_type = 'App\\Models\\Article'
    LEFT JOIN file_infos fi
      ON fi.attachment_id = att.id
    WHERE a.id = ?
    ON DUPLICATE KEY UPDATE
        text = VALUES(text),
        updated_at = NOW()
SQL, [$articleId]);
    }
}
