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

use KonradMichalik\Typo3LetterAvatar\Enum\ImageDriver;
use KonradMichalik\Typo3LetterAvatar\Image\Avatar;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\{Gd, Gmagick, Imagick};
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * AvatarTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class AvatarTest extends TestCase
{
    protected function setUp(): void
    {
        // Mock TYPO3_CONF_VARS for image driver configuration
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] = 'ImageMagick';
    }

    #[Test]
    public function createReturnsImagickDriverByDefault(): void
    {
        $avatar = Avatar::create(name: 'John Doe');

        self::assertInstanceOf(Imagick::class, $avatar);
    }

    #[Test]
    public function createReturnsGdDriverWhenSpecified(): void
    {
        $avatar = Avatar::create(name: 'John Doe', imageDriver: ImageDriver::GD);

        self::assertInstanceOf(Gd::class, $avatar);
    }

    #[Test]
    public function createReturnsGmagickDriverWhenSpecified(): void
    {
        $avatar = Avatar::create(name: 'John Doe', imageDriver: ImageDriver::GMAGICK);

        self::assertInstanceOf(Gmagick::class, $avatar);
    }

    #[Test]
    public function createReturnsImagickDriverWhenSpecified(): void
    {
        $avatar = Avatar::create(name: 'John Doe', imageDriver: ImageDriver::IMAGICK);

        self::assertInstanceOf(Imagick::class, $avatar);
    }

    #[Test]
    public function createUsesGlobalProcessorConfiguration(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] = ImageDriver::GMAGICK;

        $avatar = Avatar::create(name: 'John Doe');

        self::assertInstanceOf(Gmagick::class, $avatar);
    }

    #[Test]
    public function createWithGdConfiguration(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] = ImageDriver::GD;

        $avatar = Avatar::create(name: 'John Doe');

        self::assertInstanceOf(Gd::class, $avatar);
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

    #[Test]
    public function createIgnoresImageDriverInPassedArguments(): void
    {
        // When imageDriver is explicitly passed, it should be used and not passed to the constructor
        $avatar = Avatar::create(
            name: 'Test User',
            imageDriver: ImageDriver::GD,
        );

        self::assertInstanceOf(Gd::class, $avatar);
        self::assertSame('Test User', $avatar->name);
    }

    #[Test]
    public function createFallsBackToImagickForUnknownDriver(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'] = 'unknown_processor';

        $avatar = Avatar::create(name: 'John Doe');

        // Should fall back to Imagick (default case)
        self::assertInstanceOf(Imagick::class, $avatar);
    }
}
