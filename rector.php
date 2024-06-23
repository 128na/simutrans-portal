<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/lang',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
    )
    ->withSkip([
        IssetOnPropertyObjectToPropertyExistsRector::class, // property_exists($model, 'hoge') return false
        ReturnTypeFromStrictTypedCallRector::class => [__DIR__.'/app/Repositories/BaseRepository.php'], // Generics壊れる
    ]);
