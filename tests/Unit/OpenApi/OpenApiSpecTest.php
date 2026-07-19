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
     * @return array<string, array{0: string, 1: list<array{name: string, type: string, format?: string}>}>
     */
    public static function schemaPropertyProvider(): array
    {
        return [
            'Article' => ['Article', [
                ['name' => 'id', 'type' => 'integer'],
                ['name' => 'title', 'type' => 'string'],
                ['name' => 'slug', 'type' => 'string'],
                ['name' => 'status', 'type' => 'string'],
                ['name' => 'post_type', 'type' => 'string'],
                ['name' => 'contents', 'type' => 'object'],
                ['name' => 'categories', 'type' => 'array'],
                ['name' => 'tags', 'type' => 'array'],
                ['name' => 'articles', 'type' => 'array'],
                ['name' => 'attachments', 'type' => 'array'],
                ['name' => 'created_at', 'type' => 'string', 'format' => 'date-time'],
                ['name' => 'published_at', 'type' => 'string', 'format' => 'date-time'],
                ['name' => 'modified_at', 'type' => 'string', 'format' => 'date-time'],
            ]],
            'Attachment' => ['Attachment', [
                ['name' => 'id', 'type' => 'integer'],
                ['name' => 'attachmentable_type', 'type' => 'string'],
                ['name' => 'attachmentable_id', 'type' => 'integer'],
                ['name' => 'type', 'type' => 'string'],
                ['name' => 'original_name', 'type' => 'string'],
                ['name' => 'thumbnail', 'type' => 'string'],
                ['name' => 'url', 'type' => 'string'],
                ['name' => 'size', 'type' => 'integer'],
                ['name' => 'fileInfo', 'type' => 'object'],
                ['name' => 'caption', 'type' => 'string'],
                ['name' => 'order', 'type' => 'integer'],
                ['name' => 'attachmentable', 'type' => 'object'],
                ['name' => 'created_at', 'type' => 'string', 'format' => 'date-time'],
            ]],
            'User' => ['User', [
                ['name' => 'id', 'type' => 'integer'],
                ['name' => 'name', 'type' => 'string'],
                ['name' => 'nickname', 'type' => 'string'],
                ['name' => 'role', 'type' => 'string'],
                ['name' => 'profile', 'type' => 'object'],
            ]],
            'Category' => ['Category', [
                ['name' => 'id', 'type' => 'integer'],
                ['name' => 'name', 'type' => 'string'],
            ]],
            'Tag' => ['Tag', [
                ['name' => 'id', 'type' => 'integer'],
                ['name' => 'name', 'type' => 'string'],
            ]],
            'Error' => ['Error', [
                ['name' => 'message', 'type' => 'string'],
            ]],
            'ProfileEdit' => ['ProfileEdit', [
                ['name' => 'nickname', 'type' => 'string'],
            ]],
        ];
    }

    /**
     * @param  list<array{name: string, type: string, format?: string}>  $expectedProperties
     */
    #[DataProvider('schemaPropertyProvider')]
    public function test_schema_has_expected_properties(string $schemaName, array $expectedProperties): void
    {
        $generator = new Generator(new NullLogger);
        $openApi = $generator->generate([app_path('OpenApi')]);

        $schema = $this->findSchema($openApi, $schemaName);

        $this->assertNotNull($schema, "Schema \"{$schemaName}\" was not found in the generated OpenAPI spec.");

        $actualProperties = collect($schema->properties ?? [])
            ->keyBy(fn ($property): string => (string) $property->property);

        foreach ($expectedProperties as $expected) {
            $property = $actualProperties->get($expected['name']);

            $this->assertNotNull(
                $property,
                "Schema \"{$schemaName}\" is missing expected property \"{$expected['name']}\"."
            );

            $this->assertSame(
                $expected['type'],
                $property->type,
                "Schema \"{$schemaName}\" property \"{$expected['name']}\" has unexpected type."
            );

            if (isset($expected['format'])) {
                $this->assertSame(
                    $expected['format'],
                    $property->format,
                    "Schema \"{$schemaName}\" property \"{$expected['name']}\" has unexpected format."
                );
            }
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
