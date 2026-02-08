<?php

declare(strict_types=1);

namespace Tests\Unit\OpenApi;

use OpenApi\Generator;
use Psr\Log\NullLogger;
use Tests\Unit\TestCase;

class OpenApiSpecTest extends TestCase
{
    public function test_openapi_schemas_are_generated(): void
    {
        $generator = new Generator(new NullLogger);
        $openApi = $generator->generate([app_path('OpenApi')]);

        $this->assertNotNull($openApi);
        $this->assertSame('Simutrans Portal API', $openApi->info->title);
        $this->assertNotNull($openApi->components);
        $this->assertNotNull($openApi->components->schemas);
    }
}
