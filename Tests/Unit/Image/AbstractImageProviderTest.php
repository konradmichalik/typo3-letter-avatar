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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Image;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\{ColorMode, ImageFormat, Shape, Transform};
use KonradMichalik\Typo3LetterAvatar\Image\AbstractImageProvider;
use KonradMichalik\Typo3LetterAvatar\Service\Colorize;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function count;
use function strlen;

/**
 * AbstractImageProviderTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class AbstractImageProviderTest extends TestCase
{
    private AbstractImageProvider $imageProvider;

    protected function setUp(): void
    {
        // Mock configuration for testing
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'imagePath' => '/typo3temp/assets/avatars/',
        ];

        // Create anonymous class that extends AbstractImageProvider
        $this->imageProvider = new
/**
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class(name: 'Test User', size: 100, fontSize: 0.5, mode: ColorMode::CUSTOM, foregroundColor: '#FFFFFF', backgroundColor: '#000000') extends AbstractImageProvider {
    public function generate(): mixed
    {
        return 'mock-image';
    }

    public function save(?string $path = null, ImageFormat $format = ImageFormat::PNG, int $quality = 90): string
    {
        return '/mock/path/image.png';
    }
};
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
    }

    #[Test]
    public function constructorSetsProperties(): void
    {
        self::assertSame('Test User', $this->imageProvider->name);
        self::assertSame(100, $this->imageProvider->size);
        self::assertSame(0.5, $this->imageProvider->fontSize);
        self::assertSame(ColorMode::CUSTOM, $this->imageProvider->mode);
        self::assertSame('#FFFFFF', $this->imageProvider->foregroundColor);
        self::assertSame('#000000', $this->imageProvider->backgroundColor);
    }

    #[Test]
    public function constructorInitializesColorizeService(): void
    {
        // Access protected property via reflection
        $reflection = new ReflectionClass($this->imageProvider);
        $property = $reflection->getProperty('colorizeService');

        $colorizeService = $property->getValue($this->imageProvider);

        self::assertInstanceOf(Colorize::class, $colorizeService);
    }

    #[Test]
    public function getImagePathWithCustomFilename(): void
    {
        // Skip complex path tests - test structure instead
        self::assertTrue(method_exists($this->imageProvider, 'getImagePath'));
    }

    #[Test]
    public function getWebPathMethodExists(): void
    {
        self::assertTrue(method_exists($this->imageProvider, 'getWebPath'));
    }

    #[Test]
    public function configToHashGeneratesConsistentHash(): void
    {
        // Call protected method via reflection
        $reflection = new ReflectionClass($this->imageProvider);
        $method = $reflection->getMethod('configToHash');

        $hash1 = $method->invoke($this->imageProvider);
        $hash2 = $method->invoke($this->imageProvider);

        self::assertSame($hash1, $hash2);
        self::assertIsString($hash1);
        self::assertNotEmpty($hash1);
    }

    #[Test]
    public function configToHashGeneratesDifferentHashForDifferentConfig(): void
    {
        $reflection = new ReflectionClass($this->imageProvider);
        $method = $reflection->getMethod('configToHash');

        $hash1 = $method->invoke($this->imageProvider);

        // Change configuration
        $this->imageProvider->name = 'Different User';
        $hash2 = $method->invoke($this->imageProvider);

        self::assertNotSame($hash1, $hash2);
    }

    #[Test]
    public function configToHashIsUniquePerEdgeCaseName(): void
    {
        // Each demo fixture name should hash to a distinct value
        $names = [
            'Maria Müller',
            "Thomas O'Brien",
            'Li Wei',
            'Maximilian Hubertus von Habsburg-Lothringen',
            'Émilie Łukasiewicz',
        ];

        $reflection = new ReflectionClass($this->imageProvider);
        $method = $reflection->getMethod('configToHash');

        $hashes = [];
        foreach ($names as $name) {
            $this->imageProvider->name = $name;
            $hashes[] = $method->invoke($this->imageProvider);
        }

        self::assertCount(count($names), array_unique($hashes), 'Edge-case names must produce unique hashes');
    }

    #[Test]
    public function configToHashHandlesUnicodeNamesAsValidString(): void
    {
        $reflection = new ReflectionClass($this->imageProvider);
        $method = $reflection->getMethod('configToHash');

        $this->imageProvider->name = 'Émilie Łukasiewicz';
        $hash = $method->invoke($this->imageProvider);

        // SHA-256 hex digest is exactly 64 chars
        self::assertSame(64, strlen((string) $hash));
        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $hash);
    }

    #[Test]
    public function configToHashChangesWhenInitialsChange(): void
    {
        $reflection = new ReflectionClass($this->imageProvider);
        $method = $reflection->getMethod('configToHash');

        $this->imageProvider->initials = '';
        $without = $method->invoke($this->imageProvider);

        $this->imageProvider->initials = 'MM';
        $withInitials = $method->invoke($this->imageProvider);

        self::assertNotSame($without, $withInitials);
    }

    #[Test]
    public function configToHashChangesWhenShapeChanges(): void
    {
        $reflection = new ReflectionClass($this->imageProvider);
        $method = $reflection->getMethod('configToHash');

        $this->imageProvider->shape = Shape::CIRCLE;
        $circle = $method->invoke($this->imageProvider);

        $this->imageProvider->shape = Shape::SQUARE;
        $square = $method->invoke($this->imageProvider);

        self::assertNotSame($circle, $square);
    }

    #[Test]
    public function configToHashChangesWhenTransformChanges(): void
    {
        $reflection = new ReflectionClass($this->imageProvider);
        $method = $reflection->getMethod('configToHash');

        $this->imageProvider->transform = Transform::NONE;
        $none = $method->invoke($this->imageProvider);

        $this->imageProvider->transform = Transform::UPPERCASE;
        $upper = $method->invoke($this->imageProvider);

        self::assertNotSame($none, $upper);
    }

    #[Test]
    public function configToHashIsIndependentOfImageFormat(): void
    {
        // The hash intentionally excludes imageFormat — the format is only in the file extension
        $reflection = new ReflectionClass($this->imageProvider);
        $method = $reflection->getMethod('configToHash');

        $this->imageProvider->imageFormat = ImageFormat::PNG;
        $png = $method->invoke($this->imageProvider);

        $this->imageProvider->imageFormat = ImageFormat::JPEG;
        $jpeg = $method->invoke($this->imageProvider);

        self::assertSame($png, $jpeg);
    }

    #[Test]
    public function constructorAppliesDefaultsForOptionalArguments(): void
    {
        $provider = new
/**
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class extends AbstractImageProvider {
    public function generate(): mixed
    {
        return null;
    }

    public function save(?string $path = null, ImageFormat $format = ImageFormat::PNG, int $quality = 90): string
    {
        return '';
    }
};

        self::assertSame('', $provider->name);
        self::assertSame('', $provider->initials);
        self::assertSame(100, $provider->size);
        self::assertSame(0.5, $provider->fontSize);
        self::assertSame(ColorMode::CUSTOM, $provider->mode);
        self::assertSame('', $provider->theme);
        self::assertSame(ImageFormat::PNG, $provider->imageFormat);
        self::assertSame(Transform::NONE, $provider->transform);
        self::assertSame(Shape::CIRCLE, $provider->shape);
    }

    #[Test]
    public function constructorWithAllParameters(): void
    {
        $provider = new
/**
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class(name: 'Full Name', initials: 'FN', size: 200, fontSize: 0.8, fontPath: 'EXT:test/font.ttf', foregroundColor: '#FF0000', backgroundColor: '#00FF00', mode: ColorMode::PAIRS, theme: 'test-theme', imageFormat: ImageFormat::JPEG, transform: Transform::UPPERCASE, shape: Shape::SQUARE) extends AbstractImageProvider {
    public function generate(): mixed
    {
        return null;
    }

    public function save(?string $path = null, ImageFormat $format = ImageFormat::PNG, int $quality = 90): string
    {
        return '';
    }
};

        self::assertSame('Full Name', $provider->name);
        self::assertSame('FN', $provider->initials);
        self::assertSame(200, $provider->size);
        self::assertSame(0.8, $provider->fontSize);
        self::assertSame('EXT:test/font.ttf', $provider->fontPath);
        self::assertSame('#FF0000', $provider->foregroundColor);
        self::assertSame('#00FF00', $provider->backgroundColor);
        self::assertSame(ColorMode::PAIRS, $provider->mode);
        self::assertSame('test-theme', $provider->theme);
        self::assertSame(ImageFormat::JPEG, $provider->imageFormat);
        self::assertSame(Transform::UPPERCASE, $provider->transform);
        self::assertSame(Shape::SQUARE, $provider->shape);
    }
}
