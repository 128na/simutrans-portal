<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
class UserAttachmentListTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        ログイン中のユーザーがアップロードした添付ファイル一覧を返します。

        記事作成時に thumbnail_id・file_id として指定するattachment IDを確認するために使用します。
        ファイルのアップロード自体はWebUI（マイページ > 添付ファイル管理）または POST /api/v2/attachments で行ってください。

        ## レスポンス（配列）
        - id: attachment ID
        - original_name: 元のファイル名
        - is_image: 画像ファイルかどうか
        - created_at: アップロード日時（ISO 8601）
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $user = $request->user();
        if (! $user instanceof User) {
            return Response::error('Unauthorized.');
        }

        $attachments = $user->myAttachments()->latest()->get();

        return Response::json(
            $attachments->map(fn (Attachment $a): array => [
                'id' => $a->id,
                'original_name' => $a->original_name,
                'is_image' => $a->is_image,
                'created_at' => $a->created_at?->toIso8601String(),
            ])->values()->all()
        );
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
