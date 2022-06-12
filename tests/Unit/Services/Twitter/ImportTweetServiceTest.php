<?php

namespace Tests\Unit\Services\Twitter;

use App\Services\Twitter\ImportTweetService;
use Tests\UnitTestCase;

class ImportTweetServiceTest extends UnitTestCase
{
    private function getSUT(): ImportTweetService
    {
        return app(ImportTweetService::class);
    }

    public function testImportJson()
    {
        $result = $this->getSUT()->importJson(__DIR__.'/dummy.json');
        $this->assertCount(1, $result);
    }
}
