<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\Validator;
use Mockery;
use Tests\CreatesApplication;
use Tests\TestCase as TestsTestCase;

abstract class TestCase extends TestsTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @param  class-string<\Illuminate\Foundation\Http\FormRequest>  $requestClass
     * @param  array<mixed>  $data
     */
    protected function makeValidator(string $requestClass, array $data): \Illuminate\Validation\Validator
    {
        $request = new $requestClass($data);

        return Validator::make($data, $request->rules());
    }
}
