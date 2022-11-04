<?php

namespace Tests\Unit\Services;

use App\Services\LogMappingService;
use Tests\UnitTestCase;

class LogMappingServiceTest extends UnitTestCase
{
    private function getSUT(): LogMappingService
    {
        return app(LogMappingService::class);
    }

    public function test()
    {
        $data = [
            'n' => 'dummy name',
            'm' => 'dummy message',
            'i' => 'dummy info',
            's' => 'dummy stack',
            'l' => 'dummy location',
            'z' => 'dummy undefined',
        ];
        $actual = $this->getSUT()->mapping($data);
        $expected = [
            'name' => 'dummy name',
            'message' => 'dummy message',
            'info' => 'dummy info',
            'stack' => 'dummy stack',
            'location' => 'dummy location',
        ];
        $this->assertEquals($expected, $actual);
    }

    public function testAllowUndefined()
    {
        $data = [
            'n' => 'dummy name',
            'm' => 'dummy message',
            'i' => 'dummy info',
            's' => 'dummy stack',
            'l' => 'dummy location',
            'z' => 'dummy undefined',
        ];
        $actual = $this->getSUT()->mapping($data, false);
        $expected = [
            'name' => 'dummy name',
            'message' => 'dummy message',
            'info' => 'dummy info',
            'stack' => 'dummy stack',
            'location' => 'dummy location',
            'z' => 'dummy undefined',
        ];
        $this->assertEquals($expected, $actual);
    }
}
