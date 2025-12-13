<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Isset_\IssetOnPropertyObjectToPropertyExistsRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\Concat\RemoveConcatAutocastRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/lang',
        __DIR__.'/resources',
        __DIR__.'/routes',
        // __DIR__ . '/tests',
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
        carbon: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withSkip([
        IssetOnPropertyObjectToPropertyExistsRector::class, // property_exists($model, 'hoge') return false
        ReturnTypeFromStrictTypedCallRector::class => [__DIR__.'/app/Repositories/BaseRepository.php'], // Generics壊れる
        RemoveUnusedVariableAssignRector::class => [__DIR__.'/tests'], // $selfでのアクセスが壊れるので
        // NewlineAfterStatementRector::class => [__DIR__ . '/tests'], // $selfでのアクセスが壊れるので
        DeclareStrictTypesRector::class => [__DIR__.'/resources/views'], // bladeファイルも declare がつくので
        StringToClassConstantRector::class, // view('auth.login') が誤って変換されるため
        PostIncDecToPreIncDecRector::class,
        RemoveConcatAutocastRector::class,
        ReadOnlyPropertyRector::class,
        // Laravel 12 + Rector互換性問題: Illuminate\Foundation\Support\Providers\EventServiceProvider解析エラー回避
        // __DIR__.'/app/Actions',
        // __DIR__.'/app/Console/Commands',
        // __DIR__.'/app/Http/Resources',
        // __DIR__.'/app/Models',
        // __DIR__.'/app/Providers',
        // __DIR__.'/app/Repositories',
        // __DIR__.'/app/Services',
    ])
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,

        LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,

        // LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER, 完全クラス名が冗長なのでなし
        // LaravelSetList::LARAVEL_IF_HELPERS, phpstanがちょくちょくエラーになるので
        // LaravelSetList::LARAVEL_STATIC_TO_INJECTION, ぶっ壊れる
    ]);
