<?php

declare(strict_types=1);

/*
 * This file is part of the "typo3_letter_avatar" TYPO3 CMS extension.
 *
 * (c) Konrad Michalik <hej@konradmichalik.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Rector\Config\RectorConfig;
use Rector\PostRector\Rector\NameImportingPostRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\ValueObject\PhpVersion;
use Ssch\TYPO3Rector\CodeQuality\General\{ConvertImplicitVariablesToExplicitGlobalsRector,
    ExtEmConfRector,
    GeneralUtilityMakeInstanceToConstructorPropertyRector};
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\Set\{Typo3LevelSetList, Typo3SetList};

$rootPath = dirname(__DIR__, 2);

return RectorConfig::configure()
    ->withPaths([
        $rootPath.'/Classes',
        $rootPath.'/Configuration',
        $rootPath.'/ext_emconf.php',
        $rootPath.'/Tests/Unit',
    ])
    ->withPhpVersion(PhpVersion::PHP_81)
    ->withSets([
        Typo3SetList::CODE_QUALITY,
        Typo3SetList::GENERAL,
        Typo3LevelSetList::UP_TO_TYPO3_13,
        LevelSetList::UP_TO_PHP_81,
    ])
    ->withPHPStanConfigs([
        Typo3Option::PHPSTAN_FOR_RECTOR_PATH,
    ])
    ->withRules([
        AddVoidReturnTypeWhereNoReturnRector::class,
        ConvertImplicitVariablesToExplicitGlobalsRector::class,
    ])
    ->withConfiguredRule(ExtEmConfRector::class, [
        ExtEmConfRector::PHP_VERSION_CONSTRAINT => '8.1.0-8.4.99',
        ExtEmConfRector::TYPO3_VERSION_CONSTRAINT => '11.5.0-13.4.99',
        ExtEmConfRector::ADDITIONAL_VALUES_TO_BE_REMOVED => [],
    ])
    ->withSkip([
        $rootPath.'/**/Configuration/ExtensionBuilder/*',
        NameImportingPostRector::class => [
            'ClassAliasMap.php',
        ],
        GeneralUtilityMakeInstanceToConstructorPropertyRector::class,
    ])
;
