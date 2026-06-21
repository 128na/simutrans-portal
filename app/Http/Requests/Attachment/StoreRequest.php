<?php

declare(strict_types=1);

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreRequest extends FormRequest
{
    /**
     * Simutransアドオン・スクリーンショット・設定ファイルとして実運用されている拡張子のみ許可する。
     *
     * @var list<string>
     */
    private const ALLOWED_EXTENSIONS = [
        'png', 'jpg', 'jpeg', 'jfif', 'ppm',
        'zip', '7z',
        'pak', 'tab',
        'txt', 'pdf',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                // .pak/.tab はSymfonyのMIME種別辞書に存在しないため mimes:/File::types() ではなく
                // 拡張子ベースの extensions() で判定する。グローバルな File::defaults() の影響を受けないよう
                // new File() で素の状態から組み立てる。
                (new File)->extensions(self::ALLOWED_EXTENSIONS)->max('1gb'),
            ],
        ];
    }
}
