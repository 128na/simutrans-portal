<?php

declare(strict_types=1);

namespace App\Jobs\StaticJson;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

abstract class BaseGenerator implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
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
        $json = json_encode($data);
        if ($json) {
            Storage::disk('public')->put('json/'.$filename, $json);
        }
    }
}
