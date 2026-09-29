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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\ViewHelpers;

use InvalidArgumentException;
use KonradMichalik\Ttt\Attribute\{WithEnvironment, WithTypo3ConfVars};
use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\{ImageFormat, Shape, Transform};
use KonradMichalik\Typo3LetterAvatar\ViewHelpers\AvatarViewHelper;
use PHPUnit\Framework\Attributes\{DataProvider, Test};
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function dirname;
use function extension_loaded;

/**
 * AvatarViewHelperRenderTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithEnvironment(projectPath: 'self', temporaryProjectPath: false)]
#[WithTypo3ConfVars([
    'SYS' => [
        'folderCreateMask' => '2775',
        'fileCreateMask' => '0664',
        'encryptionKey' => 'test-encryption-key',
    ],
    'EXTENSIONS' => [Configuration::EXT_KEY => [
        'colorMode' => 'stringify',
        'theme' => 'colorful',
    ]],
    'EXTCONF' => [Configuration::EXT_KEY => ['configuration' => [
        'size' => 50,
        'fontSize' => 0.5,
        'imagePath' => self::IMAGE_PATH,
        'imageFormat' => ImageFormat::PNG,
        'shape' => Shape::CIRCLE,
        'transform' => Transform::NONE,
        'random' => [
            'foregrounds' => ['#FFFFFF'],
            'backgrounds' => ['#FF0000', '#00FF00'],
        ],
        'themes' => [
            'colorful' => [
                'foregrounds' => ['#FFFFFF'],
                'backgrounds' => ['#2196F3'],
            ],
        ],
    ]]],
])]
final class AvatarViewHelperRenderTest extends TestCase
{
    private const IMAGE_PATH = '/.Build/var/tests/avatars/';

    protected function setUp(): void
    {
        if (!extension_loaded('gd')) {
            self::markTestSkipped('ext-gd is not available.');
        }
    }

    protected function tearDown(): void
    {
        GeneralUtility::rmdir(Environment::getPublicPath().self::IMAGE_PATH, true);
    }

    #[Test]
    public function renderFallsBackToConfiguredColorModeWhenModeArgumentIsMissing(): void
    {
        $webPath = $this->render(['name' => 'John Doe']);

        self::assertStringEndsWith('.png', $webPath);
        self::assertFileExists(Environment::getPublicPath().$webPath);
    }

    #[Test]
    public function renderPassesForegroundAndBackgroundColorArguments(): void
    {
        $webPath = $this->render([
            'name' => 'John Doe',
            'mode' => 'custom',
            'foregroundColor' => '#000000',
            'backgroundColor' => '#FFFFFF',
        ]);

        self::assertFileExists(Environment::getPublicPath().$webPath);
    }

    #[Test]
    public function renderAppliesShapeAndTransformArguments(): void
    {
        $webPath = $this->render([
            'initials' => 'jd',
            'shape' => 'square',
            'transform' => 'uppercase',
        ]);

        self::assertFileExists(Environment::getPublicPath().$webPath);
    }

    #[Test]
    public function renderReusesExistingAvatarFile(): void
    {
        $webPath = $this->render(['name' => 'John Doe']);
        $file = Environment::getPublicPath().$webPath;
        $firstModificationTime = filemtime($file);

        self::assertSame($webPath, $this->render(['name' => 'John Doe']));
        self::assertSame($firstModificationTime, filemtime($file));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function invalidEnumArgumentProvider(): array
    {
        return [
            'mode' => ['mode', 'nonsense'],
            'imageFormat' => ['imageFormat', 'gif'],
            'transform' => ['transform', 'capitalize'],
            'shape' => ['shape', 'triangle'],
        ];
    }

    #[Test]
    #[DataProvider('invalidEnumArgumentProvider')]
    public function renderThrowsExceptionForInvalidEnumArgument(string $argument, string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1751719200);

        $this->render(['name' => 'John Doe', $argument => $value]);
    }

    /**
     * @param array<string, mixed> $arguments
     */
    private function render(array $arguments): string
    {
        // Absolute path needs dirname(), which is not allowed in attribute arguments
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Configuration::EXT_KEY]['fontPath'] = dirname(__DIR__, 3).'/Resources/Public/Fonts/NotoSans-Bold.ttf';

        $viewHelper = new AvatarViewHelper();
        $viewHelper->initializeArguments();

        $viewHelper->setArguments($arguments);

        return $viewHelper->render();
    }
}
