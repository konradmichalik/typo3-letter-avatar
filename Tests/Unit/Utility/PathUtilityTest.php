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
use TYPO3\CMS\Core\Core\{ApplicationContext, Environment};
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function dirname;

/**
 * PathUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class PathUtilityTest extends TestCase
{
    private const IMAGE_PATH = '/.Build/var/tests/pathutil/';

    public static function setUpBeforeClass(): void
    {
        $extensionRoot = dirname(__DIR__, 3);
        Environment::initialize(
            new ApplicationContext('Testing'),
            true,
            false,
            $extensionRoot,
            $extensionRoot,
            $extensionRoot.'/.Build/var',
            $extensionRoot.'/.Build/var/config',
            $extensionRoot.'/.Build/public/index.php',
            'UNIX',
        );
    }

    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['folderCreateMask'] = '2775';
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'imagePath' => self::IMAGE_PATH,
        ];
    }

    protected function tearDown(): void
    {
        GeneralUtility::rmdir(Environment::getPublicPath().self::IMAGE_PATH, true);
        unset(
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['folderCreateMask'],
        );
    }

    #[Test]
    public function getImageFolderReturnsPublicPathWithConfiguredImagePath(): void
    {
        $expected = Environment::getPublicPath().self::IMAGE_PATH;

        self::assertSame($expected, PathUtility::getImageFolder());
    }

    #[Test]
    public function getImageFolderCreatesTheDirectoryWhenMissing(): void
    {
        $folder = Environment::getPublicPath().self::IMAGE_PATH;
        self::assertDirectoryDoesNotExist($folder);

        PathUtility::getImageFolder();

        self::assertDirectoryExists($folder);
    }

    #[Test]
    public function getImageFolderAppendsTrailingSlashWhenMissing(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['imagePath'] = rtrim(self::IMAGE_PATH, '/');

        self::assertStringEndsWith('/', PathUtility::getImageFolder());
    }

    #[Test]
    public function getWebPathReturnsPathContainingImagePathAndFilename(): void
    {
        $webPath = PathUtility::getWebPath('avatar.png');

        self::assertStringContainsString('avatar.png', $webPath);
        self::assertStringNotContainsString('//avatar.png', $webPath);
    }

    #[Test]
    public function getWebPathStripsLeadingSlashFromFilename(): void
    {
        $withSlash = PathUtility::getWebPath('/avatar.png');
        $withoutSlash = PathUtility::getWebPath('avatar.png');

        self::assertSame($withoutSlash, $withSlash);
    }

    #[Test]
    public function getWebPathAppendsTrailingSlashToImagePathWhenMissing(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['imagePath'] = rtrim(self::IMAGE_PATH, '/');

        $webPath = PathUtility::getWebPath('avatar.png');

        self::assertStringEndsWith('/avatar.png', $webPath);
    }

    #[Test]
    public function pathUtilityMethodsAreStatic(): void
    {
        $reflectionClass = new ReflectionClass(PathUtility::class);

        self::assertTrue($reflectionClass->getMethod('getImageFolder')->isStatic());
        self::assertTrue($reflectionClass->getMethod('getWebPath')->isStatic());
    }
}
