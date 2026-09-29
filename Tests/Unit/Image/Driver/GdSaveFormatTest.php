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

use KonradMichalik\Ttt\Attribute\{WithEnvironment, WithTypo3ConfVars};
use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\Gd;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function dirname;
use function extension_loaded;

/**
 * GdSaveFormatTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars([
    'SYS' => [
        'folderCreateMask' => '2775',
        'fileCreateMask' => '0664',
        'encryptionKey' => 'test-encryption-key',
    ],
    'EXTENSIONS' => [Configuration::EXT_KEY => []],
])]
#[WithEnvironment(projectPath: 'self', temporaryProjectPath: false)]
final class GdSaveFormatTest extends TestCase
{
    private const IMAGE_PATH = '/.Build/var/tests/save-format/';

    // Infection runs tests in parallel processes; a shared directory lets them delete each other's files
    private string $imagePath;

    protected function setUp(): void
    {
        if (!extension_loaded('gd')) {
            self::markTestSkipped('ext-gd is not available.');
        }

        $this->imagePath = self::IMAGE_PATH.bin2hex(random_bytes(8)).'/';
    }

    protected function tearDown(): void
    {
        GeneralUtility::rmdir(Environment::getPublicPath().$this->imagePath, true);
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
        $path = Environment::getPublicPath().$this->imagePath.'explicit.jpeg';
        GeneralUtility::mkdir_deep(Environment::getPublicPath().$this->imagePath);

        $avatar->save($path, ImageFormat::JPEG);

        self::assertSame(\IMAGETYPE_JPEG, exif_imagetype($path));
    }

    private function createAvatar(ImageFormat $format): Gd
    {
        // Not known at compile time, restored by the class-level #[WithTypo3ConfVars]
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['imagePath'] = $this->imagePath;

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
