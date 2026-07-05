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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Image\Driver;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\Gd;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Core\{ApplicationContext, Environment};
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function dirname;
use function extension_loaded;

/**
 * GdSaveFormatTest.
 *
 * Ensures save() writes the file using the configured image format instead of
 * always defaulting to PNG, so getImagePath() and the written file stay in sync.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class GdSaveFormatTest extends TestCase
{
    private const IMAGE_PATH = '/.Build/var/tests/save-format/';

    public static function setUpBeforeClass(): void
    {
        $extensionRoot = dirname(__DIR__, 4);
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
        if (!extension_loaded('gd')) {
            self::markTestSkipped('ext-gd is not available.');
        }

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['folderCreateMask'] = '2775';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['fileCreateMask'] = '0664';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] = 'test-encryption-key';
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY] = [];
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'imagePath' => self::IMAGE_PATH,
        ];
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY],
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['folderCreateMask'],
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['fileCreateMask'],
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'],
        );

        GeneralUtility::rmdir(Environment::getPublicPath().self::IMAGE_PATH, true);
    }

    #[Test]
    public function saveWritesJpegFileMatchingImagePathWhenConfiguredAsJpeg(): void
    {
        $avatar = $this->createAvatar(ImageFormat::JPEG);

        $writtenPath = $avatar->save();

        self::assertSame($avatar->getImagePath(), $writtenPath);
        self::assertStringEndsWith('.jpeg', $writtenPath);
        self::assertFileExists($writtenPath);
        self::assertSame(\IMAGETYPE_JPEG, exif_imagetype($writtenPath));
    }

    #[Test]
    public function saveWritesPngFileWhenConfiguredAsPng(): void
    {
        $avatar = $this->createAvatar(ImageFormat::PNG);

        $writtenPath = $avatar->save();

        self::assertSame($avatar->getImagePath(), $writtenPath);
        self::assertStringEndsWith('.png', $writtenPath);
        self::assertSame(\IMAGETYPE_PNG, exif_imagetype($writtenPath));
    }

    #[Test]
    public function explicitFormatArgumentOverridesConfiguredFormat(): void
    {
        $avatar = $this->createAvatar(ImageFormat::PNG);
        $path = Environment::getPublicPath().self::IMAGE_PATH.'explicit.jpeg';
        GeneralUtility::mkdir_deep(Environment::getPublicPath().self::IMAGE_PATH);

        $avatar->save($path, ImageFormat::JPEG);

        self::assertSame(\IMAGETYPE_JPEG, exif_imagetype($path));
    }

    private function createAvatar(ImageFormat $format): Gd
    {
        return new Gd(
            name: 'John Doe',
            size: 50,
            fontPath: dirname(__DIR__, 4).'/Resources/Public/Fonts/NotoSans-Bold.ttf',
            foregroundColor: '#FFFFFF',
            backgroundColor: '#000000',
            imageFormat: $format,
        );
    }
}
