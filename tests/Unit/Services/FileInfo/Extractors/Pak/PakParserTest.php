<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\PakParser;
use Tests\Unit\TestCase;

final class PakParserTest extends TestCase
{
    public function test_parse_existing_file(): void
    {
        $parser = new PakParser();
        $data = file_get_contents(__DIR__ . '/../vehicle.transparent_vehicle.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('names', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertContains('transparent_vehicle', $result['names']);
        $this->assertIsArray($result['metadata']);
        $this->assertNotEmpty($result['metadata']);
    }

    public function test_invalid_header(): void
    {
        $this->expectException(InvalidPakFileException::class);

        $parser = new PakParser();
        $parser->parse('invalid pak data');
    }

    public function test_empty_file(): void
    {
        $this->expectException(InvalidPakFileException::class);

        $parser = new PakParser();
        $parser->parse('');
    }

    public function test_metadata_structure(): void
    {
        $parser = new PakParser();
        $data = file_get_contents(__DIR__ . '/../vehicle.transparent_vehicle.pak');

        $result = $parser->parse($data);

        $this->assertNotEmpty($result['metadata']);
        $metadata = $result['metadata'][0];

        $this->assertArrayHasKey('name', $metadata);
        $this->assertArrayHasKey('copyright', $metadata);
        $this->assertArrayHasKey('objectType', $metadata);
        $this->assertArrayHasKey('compilerVersionCode', $metadata);

        $this->assertIsString($metadata['name']);
        $this->assertIsString($metadata['objectType']);
        $this->assertIsInt($metadata['compilerVersionCode']);
    }
}
