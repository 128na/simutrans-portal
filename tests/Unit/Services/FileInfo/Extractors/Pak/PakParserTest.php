<?php

declare(strict_types=1);

namespace Tests\Unit\Services\FileInfo\Extractors\Pak;

use App\Exceptions\InvalidPakFileException;
use App\Services\FileInfo\Extractors\Pak\PakParser;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\TestCase;

final class PakParserTest extends TestCase
{
    #[DataProvider('makeobjVersionProvider')]
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
            // makeobj-48 has 12 objects: bridge and factory have no name, missing test_groundobj
            'makeobj-48' => ['pakFile' => 'test-48.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'test_truck', 'test_building', 'test_citycar', 'test_good', 'test_crossing', 'test_pedestrian', 'test_tree', 'test_signal']],
            // makeobj-50 and later have 13 objects: factory has no name, bridge has test_bridge
            'makeobj-50' => ['pakFile' => 'test-50.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'test_truck', 'test_building', 'test_citycar', 'test_good', 'test_bridge', 'test_crossing', 'test_pedestrian', 'test_tree', 'test_groundobj', 'test_signal']],
            'makeobj-55.4' => ['pakFile' => 'test-55.4.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'test_truck', 'test_building', 'test_citycar', 'test_good', 'test_bridge', 'test_crossing', 'test_pedestrian', 'test_tree', 'test_groundobj', 'test_signal']],
            'makeobj-60' => ['pakFile' => 'test-60.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'test_truck', 'test_building', 'test_citycar', 'test_good', 'test_bridge', 'test_crossing', 'test_pedestrian', 'test_tree', 'test_groundobj', 'test_signal']],
            'makeobj-60.8' => ['pakFile' => 'test.pak', 'expectedNames' => ['test_1', 'test_transparent_1', 'test_truck', 'test_building', 'test_citycar', 'test_good', 'test_bridge', 'test_crossing', 'test_pedestrian', 'test_tree', 'test_groundobj', 'test_signal']],
        ];
    }

    public function test_parse_existing_file(): void
    {
        $parser = new PakParser;
        // Use the unified test.pak file
        $data = file_get_contents(__DIR__.'/../file/test.pak');

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
        $data = file_get_contents(__DIR__.'/../file/test.pak');

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

        // Test with test.pak which contains multiple object types
        $data = file_get_contents(__DIR__.'/../file/test.pak');
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
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        // Basic structure checks
        $this->assertIsArray($result);
        $this->assertArrayHasKey('names', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertContains('test_truck', $result['names']);

        // Metadata checks
        $this->assertNotEmpty($result['metadata']);

        // Find test_truck metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_truck') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_truck metadata not found in test.pak');
        $this->assertSame('test_truck', $metadata['name']);
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
        $this->assertSame(1, $vehicleData['waytype']);

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
        $data = file_get_contents(__DIR__.'/../file/test.pak');

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
        $this->assertArrayHasKey('waytype', $wayData);
        $this->assertArrayHasKey('styp', $wayData);

        // Way type string
        $this->assertArrayHasKey('waytype_str', $wayData);
        $this->assertIsString($wayData['waytype_str']);

        // System type string
        $this->assertArrayHasKey('styp_str', $wayData);
        $this->assertIsString($wayData['styp_str']);
    }

    public function test_parse_building_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_building', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_building metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_building') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_building metadata not found in test.pak');
        $this->assertSame('test_building', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        $this->assertSame('building', $metadata['objectType']);

        // Building data checks
        $this->assertArrayHasKey('buildingData', $metadata);
        $buildingData = $metadata['buildingData'];
        $this->assertIsArray($buildingData);

        // Basic building properties from test_building.dat
        $this->assertArrayHasKey('type', $buildingData);
        $this->assertArrayHasKey('level', $buildingData);
    }

    public function test_parse_citycar_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_citycar', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_citycar metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_citycar') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_citycar metadata not found in test.pak');
        $this->assertSame('test_citycar', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        // Note: citycar may be parsed as 'unknown_CCAR' depending on version
        $this->assertStringContainsString('citycar', strtolower($metadata['objectType']));
    }

    public function test_parse_good_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_good', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_good metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_good') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_good metadata not found in test.pak');
        $this->assertSame('test_good', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        $this->assertSame('good', $metadata['objectType']);

        // Good data checks
        $this->assertArrayHasKey('goodData', $metadata);
        $goodData = $metadata['goodData'];
        $this->assertIsArray($goodData);
    }

    public function test_parse_bridge_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_bridge', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_bridge metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_bridge') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_bridge metadata not found in test.pak');
        $this->assertSame('test_bridge', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        $this->assertSame('bridge', $metadata['objectType']);

        // Bridge data checks
        $this->assertArrayHasKey('bridgeData', $metadata);
        $bridgeData = $metadata['bridgeData'];
        $this->assertIsArray($bridgeData);

        // Basic bridge properties from test_bridge.dat
        $this->assertArrayHasKey('waytype', $bridgeData);
        $this->assertArrayHasKey('topspeed', $bridgeData);
        // Note: Some properties may be ignored by makeobj depending on version
    }

    public function test_parse_crossing_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_crossing', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_crossing metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_crossing') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_crossing metadata not found in test.pak');
        $this->assertSame('test_crossing', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        // Crossing is recognized as unknown_CRSS by PakParser
        $this->assertSame('unknown_CRSS', $metadata['objectType']);

        // Crossing data checks
        if (isset($metadata['crossingData'])) {
            $crossingData = $metadata['crossingData'];
            $this->assertIsArray($crossingData);
        }
    }

    public function test_parse_factory_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result['metadata']);

        // Factory in Simutrans is recognized as building with type 4
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if (
                isset($item['objectType']) &&
                $item['objectType'] === 'building' &&
                isset($item['buildingData']['type']) &&
                $item['buildingData']['type'] === 4
            ) {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'Factory metadata (building type 4) not found in test.pak');
        $this->assertSame('TestAuthor', $metadata['copyright']);
        $this->assertSame('building', $metadata['objectType']);
        $this->assertArrayHasKey('buildingData', $metadata);
        $this->assertSame(4, $metadata['buildingData']['type'], 'Building type should be 4 (Factory)');
    }

    public function test_parse_groundobj_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_groundobj', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_groundobj metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_groundobj') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_groundobj metadata not found in test.pak');
        $this->assertSame('test_groundobj', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        // Groundobj is recognized as unknown_GOBJ by PakParser
        $this->assertSame('unknown_GOBJ', $metadata['objectType']);

        if (isset($metadata['groundobjData'])) {
            $groundobjData = $metadata['groundobjData'];
            $this->assertIsArray($groundobjData);
        }
    }

    public function test_parse_pedestrian_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_pedestrian', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_pedestrian metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_pedestrian') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_pedestrian metadata not found in test.pak');
        $this->assertSame('test_pedestrian', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        // Pedestrian is recognized as unknown_PASS by PakParser
        $this->assertSame('unknown_PASS', $metadata['objectType']);

        if (isset($metadata['pedestrianData'])) {
            $pedestrianData = $metadata['pedestrianData'];
            $this->assertIsArray($pedestrianData);
        }
    }

    public function test_parse_tree_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_tree', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_tree metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_tree') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_tree metadata not found in test.pak');
        $this->assertSame('test_tree', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        $this->assertSame('tree', $metadata['objectType']);

        if (isset($metadata['treeData'])) {
            $treeData = $metadata['treeData'];
            $this->assertIsArray($treeData);
        }
    }

    public function test_parse_roadsign_metadata(): void
    {
        $parser = new PakParser;
        $data = file_get_contents(__DIR__.'/../file/test.pak');

        $result = $parser->parse($data);

        $this->assertIsArray($result);
        $this->assertContains('test_signal', $result['names']);
        $this->assertNotEmpty($result['metadata']);

        // Find test_signal metadata (test.pak contains multiple objects)
        $metadata = null;
        foreach ($result['metadata'] as $item) {
            if ($item['name'] === 'test_signal') {
                $metadata = $item;
                break;
            }
        }

        $this->assertNotNull($metadata, 'test_signal metadata not found in test.pak');
        $this->assertSame('test_signal', $metadata['name']);
        $this->assertSame('TestAuthor', $metadata['copyright']);
        // Roadsign is recognized as unknown_SIGN by PakParser
        $this->assertSame('unknown_SIGN', $metadata['objectType']);

        if (isset($metadata['roadsignData'])) {
            $roadsignData = $metadata['roadsignData'];
            $this->assertIsArray($roadsignData);
        }
    }

    // Note: skin object test is removed because the object doesn't compile with makeobj
    // The skin object requires additional properties that are not yet supported in test.dat
}
