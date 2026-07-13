<?php

declare(strict_types=1);

namespace Tests\Unit\OpenApi;

use OpenApi\Annotations\Schema;
use OpenApi\Generator;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /**
     * @return array<string, array{0: string, 1: list<string>}>
     */
    public static function schemaPropertyProvider(): array
    {
        return [
            'Article' => ['Article', [
                'id', 'title', 'slug', 'status', 'post_type', 'contents',
                'categories', 'tags', 'articles', 'attachments',
                'created_at', 'published_at', 'modified_at',
            ]],
            'Attachment' => ['Attachment', [
                'id', 'attachmentable_type', 'attachmentable_id', 'type',
                'original_name', 'thumbnail', 'url', 'size', 'fileInfo',
                'caption', 'order', 'attachmentable', 'created_at',
            ]],
            'User' => ['User', [
                'id', 'name', 'nickname', 'role', 'profile',
            ]],
            'Category' => ['Category', ['id', 'name']],
            'Tag' => ['Tag', ['id', 'name']],
            'Error' => ['Error', ['message']],
            'ProfileEdit' => ['ProfileEdit', ['nickname']],
        ];
    }

    /**
     * @param  list<string>  $expectedProperties
     */
    #[DataProvider('schemaPropertyProvider')]
    public function test_schema_has_expected_properties(string $schemaName, array $expectedProperties): void
    {
        $generator = new Generator(new NullLogger);
        $openApi = $generator->generate([app_path('OpenApi')]);

        $schema = $this->findSchema($openApi, $schemaName);

        $this->assertNotNull($schema, "Schema \"{$schemaName}\" was not found in the generated OpenAPI spec.");

        $actualProperties = collect($schema->properties ?? [])
            ->map(fn ($property): string => (string) $property->property)
            ->all();

        foreach ($expectedProperties as $expected) {
            $this->assertContains(
                $expected,
                $actualProperties,
                "Schema \"{$schemaName}\" is missing expected property \"{$expected}\"."
            );
        }
    }

    private function findSchema(mixed $openApi, string $schemaName): ?Schema
    {
        foreach ($openApi->components->schemas as $schema) {
            if ($schema instanceof Schema && $schema->schema === $schemaName) {
                return $schema;
            }
        }

        return null;
    }
}
