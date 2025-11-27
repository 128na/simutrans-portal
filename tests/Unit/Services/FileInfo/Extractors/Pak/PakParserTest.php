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
    public function test_parse_makeobj_versions(string $pakFile, array $expectedNames): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/'.$pakFile);

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('names', $result);
        $this->assertArrayHasKey('metadata', $result);

        // Verify all expected names are present
        foreach ($expectedNames as $expectedName) {
            $this->assertContains($expectedName, $result['names'], "Expected name '{$expectedName}' not found in pak file '{$pakFile}'");
        }

        $this->assertIsArray($result['metadata']);
        $this->assertNotEmpty($result['metadata']);
    }

    /**
     * @return array<string, array{pakFile: string, expectedNames: array<string>}>
     */
    public static function makeobjVersionProvider(): array
    {
        return [
            // Unified pak files containing all objects (way normal, way transparent, vehicle)
            'makeobj-48' => ['pakFile' => 'test-48.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'TestTruck']],
            'makeobj-50' => ['pakFile' => 'test-50.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'TestTruck']],
            'makeobj-55.4' => ['pakFile' => 'test-55.4.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'TestTruck']],
            'makeobj-60' => ['pakFile' => 'test-60.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'TestTruck']],
            // makeobj 60.8 creates separate pak files per object
            'makeobj-60.8-way' => ['pakFile' => 'way.test_1.pak', 'expectedNames' => ['test_1']],
            'makeobj-60.8-way-transparent' => ['pakFile' => 'way.test_transparent_1.pak', 'expectedNames' => ['test_transparent_1']],
            'makeobj-60.8-vehicle' => ['pakFile' => 'vehicle.TestTruck.pak', 'expectedNames' => ['TestTruck']],
        ];
    }

    public function test_parse_existing_file(): void
    {
        $parser = new PakParser;
        // Use one of the generated test files instead
        $data = file_get_contents(__DIR__.'/../file/way.test_transparent_1.pak');

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
        $data = file_get_contents(__DIR__.'/../file/way.test_1.pak');

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

    public function test_vehicle_metadata_not_present_for_way_objects(): void
    {
        $parser = new PakParser;

        // Test with way.test_1.pak which contains only way object
        $data = file_get_contents(__DIR__.'/../file/way.test_1.pak');
        $result = $parser->parse($data);

        $this->assertNotEmpty($result['metadata']);

        // Find the way object metadata
        $wayMetadata = null;
        foreach ($result['metadata'] as $metadata) {
            if ($metadata['objectType'] === 'way') {
                $wayMetadata = $metadata;
                break;
            }
        }

        $this->assertNotNull($wayMetadata, 'Way metadata not found');
        $this->assertArrayNotHasKey('vehicleData', $wayMetadata, 'Way object should not have vehicleData');
    }

    public function test_parse_vehicle_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/vehicle.TestTruck.pak');

        $result = $parser->parse($data);

        // Basic structure checks
        $this->assertIsArray($result);
        $this->assertArrayHasKey('names', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertContains('TestTruck', $result['names']);

        // Metadata checks
        $this->assertNotEmpty($result['metadata']);
        $metadata = $result['metadata'][0];

        $this->assertSame('TestTruck', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        $this->assertSame('vehicle', $metadata['objectType']);

        // Vehicle data checks
        $this->assertArrayHasKey('vehicleData', $metadata);
        $vehicleData = $metadata['vehicleData'];

        // Basic vehicle properties from vehicle.dat
        $this->assertSame(10, $vehicleData['capacity']);        // payload=10
        $this->assertSame(50000, $vehicleData['price']);        // cost=50000
        $this->assertSame(80, $vehicleData['topspeed']);        // speed=80
        $this->assertSame(5000, $vehicleData['weight']);        // weight=5 (tons * 1000 = kg in version 10+)
        $this->assertSame(150, $vehicleData['power']);          // power=150
        $this->assertSame(100, $vehicleData['running_cost']);   // runningcost=100
        $this->assertSame(64, $vehicleData['gear']);            // gear=100 → actual value 64 (gear calculation by makeobj)
        $this->assertSame(8, $vehicleData['len']);              // length=8
        $this->assertSame(1000, $vehicleData['loading_time']);  // default loading_time
        $this->assertSame(0, $vehicleData['maintenance']);      // default maintenance

        // Date checks (Simutrans date format: months since year 0)
        $this->assertSame(23880, $vehicleData['intro_date']);   // 1990*12 + 0 = 23880
        $this->assertSame(24251, $vehicleData['retire_date']);  // 2020*12 + 11 = 24251

        // Engine type
        $this->assertSame(1, $vehicleData['engine_type']);      // engine_type=diesel (1)
        $this->assertSame('diesel', $vehicleData['engine_type_str']); // Converted to string

        // Waytype (road=1 in Simutrans)
        $this->assertSame(1, $vehicleData['wtyp']);

        // Sound (254 = default sound index)
        $this->assertSame(254, $vehicleData['sound']);

        // Axle load and counts
        $this->assertSame(0, $vehicleData['axle_load']);
        $this->assertSame(0, $vehicleData['leader_count']);
        $this->assertSame(0, $vehicleData['trailer_count']);
        $this->assertSame(0, $vehicleData['freight_image_type']);

        // Freight type (extracted from XREF node)
        $this->assertArrayHasKey('freight_type', $vehicleData);
        $this->assertSame('goods', $vehicleData['freight_type']); // freight=goods
    }

    public function test_parse_way_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/way.test_1.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertNotEmpty($result['metadata']);

        // Check way metadata exists
        $metadata = $result['metadata'][0] ?? null;
        $this->assertNotNull($metadata);
        $this->assertArrayHasKey('wayData', $metadata);

        $wayData = $metadata['wayData'];
        $this->assertIsArray($wayData);
        $this->assertNotEmpty($wayData);

        // Basic way properties from test.dat
        $this->assertArrayHasKey('price', $wayData);
        $this->assertArrayHasKey('maintenance', $wayData);
        $this->assertArrayHasKey('topspeed', $wayData);
        $this->assertArrayHasKey('max_weight', $wayData);
        $this->assertArrayHasKey('wtyp', $wayData);
        $this->assertArrayHasKey('styp', $wayData);

        // Way type string
        $this->assertArrayHasKey('wtyp_str', $wayData);
        $this->assertIsString($wayData['wtyp_str']);

        // System type string
        $this->assertArrayHasKey('styp_str', $wayData);
        $this->assertIsString($wayData['styp_str']);
    }
}
