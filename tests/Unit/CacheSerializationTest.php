<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Cache\FileStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CacheSerializationTest extends TestCase
{
    public function test_file_cache_preserves_counters_without_restoring_objects(): void
    {
        $path = storage_path('framework/testing/cache-'.Str::uuid());
        config(['cache.stores.serialization_test' => ['driver' => 'file', 'path' => $path]]);
        $cache = Cache::store('serialization_test');
        $this->assertInstanceOf(FileStore::class, $cache->getStore());

        try {
            $cache->add('counter', 0, 60);
            $cache->increment('counter', 3);
            $this->assertSame(3, $cache->get('counter'));

            $cache->put('primitives', ['enabled' => true, 'label' => 'backup', 'missing' => null], 60);
            $this->assertSame(['enabled' => true, 'label' => 'backup', 'missing' => null], $cache->get('primitives'));

            $cache->put('object', new CacheWakeupProbe, 60);
            CacheWakeupProbe::$awakened = false;
            $this->assertInstanceOf(\__PHP_Incomplete_Class::class, $cache->get('object'));
            $this->assertFalse(CacheWakeupProbe::$awakened);
        } finally {
            File::deleteDirectory($path);
        }
    }
}

class CacheWakeupProbe
{
    public static bool $awakened = false;

    public function __wakeup(): void
    {
        self::$awakened = true;
    }
}
