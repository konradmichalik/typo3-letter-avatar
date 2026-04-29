<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "typo3_letter_avatar".
 *
 * Copyright (C) 2025-2026 Konrad Michalik <hej@konradmichalik.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 */

use KonradMichalik\PhpCsFixerPreset\Config;
use KonradMichalik\PhpCsFixerPreset\Rules\Header;
use KonradMichalik\PhpCsFixerPreset\Rules\Set\RuleSet;
use KonradMichalik\PhpDocBlockHeaderFixer\Generators\DocBlockHeader;
use KonradMichalik\PhpDocBlockHeaderFixer\Rules\DocBlockHeaderFixer;
use Symfony\Component\Finder\Finder;

$rootPath = dirname(__DIR__, 2);

return Config::create()
    ->registerCustomFixers([
        new DocBlockHeaderFixer(),
    ])
    ->withRule(
        Header::fromComposer($rootPath.'/composer.json'),
    )
    ->withRule(
        RuleSet::fromArray(
            DocBlockHeader::fromComposer($rootPath.'/composer.json')->__toArray(),
        ),
    )
    ->withFinder(
        static fn (Finder $finder) => $finder
            ->in($rootPath)
            ->notPath(['ext_emconf.php']),
    )
;
