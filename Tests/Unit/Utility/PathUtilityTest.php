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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Utility;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Utility\PathUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function define;
use function defined;

/**
 * PathUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
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
        $reflectionClass = new ReflectionClass(PathUtility::class);

        $getImageFolderMethod = $reflectionClass->getMethod('getImageFolder');
        $getWebPathMethod = $reflectionClass->getMethod('getWebPath');

        self::assertTrue($getImageFolderMethod->isStatic());
        self::assertTrue($getWebPathMethod->isStatic());
    }
}
