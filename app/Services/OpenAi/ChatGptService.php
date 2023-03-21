<?php

declare(strict_types=1);

namespace App\Services\OpenAi;

use App\Services\Service;
use Illuminate\Support\Facades\Http;
use Throwable;

class ChatGptService extends Service
{
    private string $endpoint;

    private string $key;

    public function __construct()
    {
        $this->endpoint = config('services.open_ai.endpoint');
        $this->key = config('services.open_ai.key');
    }

    private function doRequest(string $prompt): string
    {
        try {
            // todo 適切なパラメーターにする https://platform.openai.com/playground?lang=curl
            $result = Http::withToken($this->key)
                ->post($this->endpoint, [
                    'model' => 'text-davinci-003',
                    'prompt' => $prompt,
                    'temperature' => 0.7,
                    'max_tokens' => 256,
                    'top_p' => 1,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                ]);

            $data = $result->json();

            logger()->channel('file_open_ai')->info('ai', [$prompt, $data]);

            return isset($data['choices'][0]['text'])
                ? $data['choices'][0]['text']
                : '生成失敗';
        } catch (Throwable $th) {
            report($th);

            return '生成失敗';
        }
    }

    public function getDescription(string $text): string
    {
        return $this->doRequest("以下の文章を500文字程度に要約してください。\n".$text);
    }
}
