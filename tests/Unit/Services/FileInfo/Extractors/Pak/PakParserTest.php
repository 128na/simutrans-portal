<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\PakParser;
use Tests\Unit\TestCase;

final class PakParserTest extends TestCase
{
    /**
     * @dataProvider makeobjVersionProvider
     */
    public function test_parse_makeobj_versions(string $pakFile, string $expectedName): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/'.$pakFile);

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('names', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertContains($expectedName, $result['names']);
        $this->assertIsArray($result['metadata']);
        $this->assertNotEmpty($result['metadata']);
    }

    /**
     * @return array<string, array{pakFile: string, expectedName: string}>
     */
    public static function makeobjVersionProvider(): array
    {
        return [
            'makeobj-48' => ['pakFile' => 'test-48.pak', 'expectedName' => 'test_1'],
            'makeobj-50' => ['pakFile' => 'test-50.pak', 'expectedName' => 'test_1'],
            'makeobj-55.4' => ['pakFile' => 'test-55.4.pak', 'expectedName' => 'test_1'],
            'makeobj-60' => ['pakFile' => 'test-60.pak', 'expectedName' => 'test_1'],
            'makeobj-60.8' => ['pakFile' => 'test-60.8.pak', 'expectedName' => 'test_1'],
            'makeobj-48-transparent' => ['pakFile' => 'test_transparent-48.pak', 'expectedName' => 'test_transparent_1'],
            'makeobj-50-transparent' => ['pakFile' => 'test_transparent-50.pak', 'expectedName' => 'test_transparent_1'],
            'makeobj-55.4-transparent' => ['pakFile' => 'test_transparent-55.4.pak', 'expectedName' => 'test_transparent_1'],
            'makeobj-60-transparent' => ['pakFile' => 'test_transparent-60.pak', 'expectedName' => 'test_transparent_1'],
            'makeobj-60.8-transparent' => ['pakFile' => 'test_transparent-60.8.pak', 'expectedName' => 'test_transparent_1'],
        ];
    }

    public function test_parse_existing_file(): void
    {
        $parser = new PakParser;
        // Use one of the generated test files instead
        $data = file_get_contents(__DIR__.'/../file/test_transparent-60.8.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('names', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertContains('test_transparent_1', $result['names']);
        $this->assertIsArray($result['metadata']);
        $this->assertNotEmpty($result['metadata']);
    }

    public function test_invalid_header(): void
    {
        $this->expectException(InvalidPakFileException::class);

        $parser = new PakParser;
        $parser->parse('invalid pak data');
    }

    public function test_empty_file(): void
    {
        $this->expectException(InvalidPakFileException::class);

        $parser = new PakParser;
        $parser->parse('');
    }

    public function test_metadata_structure(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test-60.8.pak');

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

        // Verify actual values from test.dat
        $this->assertSame('test_1', $metadata['name']);
        $this->assertSame('128Na', $metadata['copyright']);
        $this->assertSame('way', $metadata['objectType']);
    }
}
