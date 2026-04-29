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
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Utility;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Utility\PathUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * PathUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class PathUtilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock TYPO3 configuration
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'imagePath' => '/typo3temp/assets/avatars/',
        ];

        // Mock TYPO3 Environment
        if (!defined('TYPO3_PATH_ROOT')) {
            define('TYPO3_PATH_ROOT', '/var/www/html');
        }
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
        parent::tearDown();
    }

    #[Test]
    public function getImageFolderMethodExists(): void
    {
        self::assertTrue(method_exists(PathUtility::class, 'getImageFolder'));
    }

    #[Test]
    public function getWebPathMethodExists(): void
    {
        self::assertTrue(method_exists(PathUtility::class, 'getWebPath'));
    }

    #[Test]
    public function pathUtilityMethodsAreStatic(): void
    {
        $reflectionClass = new \ReflectionClass(PathUtility::class);

        $getImageFolderMethod = $reflectionClass->getMethod('getImageFolder');
        $getWebPathMethod = $reflectionClass->getMethod('getWebPath');

        self::assertTrue($getImageFolderMethod->isStatic());
        self::assertTrue($getWebPathMethod->isStatic());
    }
}
