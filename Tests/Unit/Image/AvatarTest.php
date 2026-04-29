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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Image;

use KonradMichalik\Typo3LetterAvatar\Enum\ImageDriver;
use KonradMichalik\Typo3LetterAvatar\Image\Avatar;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\Gd;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\Gmagick;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\Imagick;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * AvatarTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class AvatarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
            fontSize: 0.6
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
            imageDriver: ImageDriver::GD
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
