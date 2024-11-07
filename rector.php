<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Privatization\Rector\Class_\FinalizeClassesWithoutChildrenRector;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->phpVersion(phpVersion: PhpVersion::PHP_81);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();
    $rectorConfig->parallel();
    $rectorConfig->symfonyContainerPhp(filePath: __DIR__.'var/cache/dev/App_KernelDevDebugContainer.php');

    $rectorConfig->autoloadPaths(autoloadPaths: [
        __DIR__.'/vendor/autoload.php',
    ]);

    $rectorConfig->paths(paths: [
        __DIR__.'/src',
        __DIR__.'/tests',
    ]);

    $rectorConfig->skip(criteria: [
        __DIR__.'/vendor',
        FinalizeClassesWithoutChildrenRector::class => [
            __DIR__.'/src/Platform/Entity/Author.php',
            __DIR__.'/src/Platform/Entity/Page.php',
            __DIR__.'/src/Platform/Entity/User.php',
        ],
    ]);

    $rectorConfig->sets(sets: [
        //        SetList::CODE_QUALITY,
        //        SetList::CODING_STYLE,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::PHP_81,
        SetList::PSR_4,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        // SetList::TYPE_DECLARATION_STRICT,
        //        SymfonySetList::SYMFONY_STRICT,
        SymfonySetList::SYMFONY_44,
        SymfonySetList::SYMFONY_50,
        SymfonySetList::SYMFONY_50_TYPES,
        SymfonySetList::SYMFONY_51,
        SymfonySetList::SYMFONY_52,
        SymfonySetList::SYMFONY_52_VALIDATOR_ATTRIBUTES,
        SymfonySetList::SYMFONY_53,
        SymfonySetList::SYMFONY_54,
        SymfonySetList::SYMFONY_60,
        SymfonySetList::SYMFONY_61,
        SymfonySetList::SYMFONY_62,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        // SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);
};
