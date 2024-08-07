<?php

declare(strict_types=1);

namespace App\Actions\GenerateStatic;

use Illuminate\Support\Facades\Storage;

abstract class BaseGenerator
{
    public function __invoke(): void
    {
        $json = $this->getJsonData();
        $filename = $this->getJsonName();

        $this->putJson($filename, $json);
    }

    /**
     * @return array<mixed>
     */
    abstract protected function getJsonData(): array;

    /**
     * @return string path to json
     */
    abstract protected function getJsonName(): string;

    /**
     * @param  array<mixed>  $data
     */
    protected function putJson(string $filename, array $data): void
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($json) {
            Storage::disk('public')->put('json/'.$filename, $json);
        }
    }
}
