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

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3LetterAvatar\Enum\ImageDriver;
use KonradMichalik\Typo3LetterAvatar\Image\Avatar;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\{Gd, Gmagick, Imagick};
use PHPUnit\Framework\Attributes\{DataProvider, Test};
use PHPUnit\Framework\TestCase;

use function sprintf;

/**
 * AvatarTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars(['GFX' => ['processor' => 'ImageMagick']])]
final class AvatarTest extends TestCase
{
    /**
     * @return array<string, array{ImageDriver, class-string}>
     */
    public static function explicitDriverProvider(): array
    {
        return [
            'GD' => [ImageDriver::GD, Gd::class],
            'ImageMagick' => [ImageDriver::IMAGICK, Imagick::class],
            'GraphicsMagick' => [ImageDriver::GMAGICK, Gmagick::class],
        ];
    }

    /**
     * @param class-string $expected
     */
    #[Test]
    #[DataProvider('explicitDriverProvider')]
    public function createUsesExplicitImageDriverOverGfxProcessor(ImageDriver $driver, string $expected): void
    {
        self::skipUnlessAvailable($expected);

        self::assertInstanceOf($expected, Avatar::create(name: 'John Doe', imageDriver: $driver));
    }

    /**
     * @return array<string, array{WithTypo3ConfVars, class-string}>
     */
    public static function gfxProcessorProvider(): array
    {
        return [
            'ImageMagick' => [new WithTypo3ConfVars(['GFX' => ['processor' => 'ImageMagick']]), Imagick::class],
            'GraphicsMagick' => [new WithTypo3ConfVars(['GFX' => ['processor' => 'GraphicsMagick']]), Gmagick::class],
            'gd' => [new WithTypo3ConfVars(['GFX' => ['processor' => 'gd']]), Gd::class],
            'unknown processor' => [new WithTypo3ConfVars(['GFX' => ['processor' => 'unknown_processor']]), Gd::class],
        ];
    }

    /**
     * @param class-string $expected
     */
    #[Test]
    #[DataProvider('gfxProcessorProvider')]
    public function createResolvesDriverFromGfxProcessor(WithTypo3ConfVars $gfx, string $expected): void
    {
        self::skipUnlessAvailable($expected);

        self::assertInstanceOf($expected, Avatar::create(name: 'John Doe'));
    }

    #[Test]
    #[WithTypo3ConfVars(['GFX' => ['processor' => 'GraphicsMagick']])]
    public function createFallsBackToGdWhenNoImageExtensionAvailable(): void
    {
        if (class_exists(\Imagick::class) || class_exists(\Gmagick::class)) {
            self::markTestSkipped('Imagick or Gmagick is loaded; cannot test GD fallback.');
        }

        self::assertInstanceOf(Gd::class, Avatar::create(name: 'John Doe'));
    }

    #[Test]
    public function createPassesArgumentsToDriver(): void
    {
        $avatar = Avatar::create(
            name: 'Test User',
            size: 100,
            fontSize: 0.6,
        );

        self::assertSame('Test User', $avatar->name);
        self::assertSame(100, $avatar->size);
        self::assertSame(0.6, $avatar->fontSize);
    }

    private static function skipUnlessAvailable(string $driver): void
    {
        $extensionClass = [Imagick::class => \Imagick::class, Gmagick::class => \Gmagick::class][$driver] ?? null;
        if (null !== $extensionClass && !class_exists($extensionClass)) {
            self::markTestSkipped(sprintf('%s is not available.', $extensionClass));
        }
    }
}
