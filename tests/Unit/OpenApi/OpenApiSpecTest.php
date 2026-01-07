<?php

declare(strict_types=1);

namespace Tests\Unit\OpenApi;

use OpenApi\Generator;
use Tests\Unit\TestCase;

class OpenApiSpecTest extends TestCase
{
    public function test_openapi_schemas_are_generated(): void
    {
        $openApi = Generator::scan([app_path('OpenApi')], [
            'logger' => new \Psr\Log\NullLogger,
        ]);

        $this->assertSame('Simutrans Portal API', $openApi->info->title);
        $this->assertNotNull($openApi->components);
        $this->assertNotNull($openApi->components->schemas);
    }
}
