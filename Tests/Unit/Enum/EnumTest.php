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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Enum;

use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Enum\EnumInterface;
use KonradMichalik\Typo3LetterAvatar\Enum\ImageDriver;
use KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat;
use KonradMichalik\Typo3LetterAvatar\Enum\Shape;
use KonradMichalik\Typo3LetterAvatar\Enum\Transform;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * EnumTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class EnumTest extends TestCase
{
    #[Test]
    public function colorModeEnumHasCorrectValues(): void
    {
        self::assertSame('stringify', ColorMode::STRINGIFY->value);
        self::assertSame('random', ColorMode::RANDOM->value);
        self::assertSame('theme', ColorMode::THEME->value);
        self::assertSame('pairs', ColorMode::PAIRS->value);
        self::assertSame('custom', ColorMode::CUSTOM->value);
    }

    #[Test]
    public function colorModeImplementsEnumInterface(): void
    {
        self::assertInstanceOf(EnumInterface::class, ColorMode::STRINGIFY);
        self::assertInstanceOf(\BackedEnum::class, ColorMode::STRINGIFY);
    }

    #[Test]
    public function colorModeTryFromReturnsCorrectEnum(): void
    {
        self::assertSame(ColorMode::STRINGIFY, ColorMode::tryFrom('stringify'));
        self::assertSame(ColorMode::RANDOM, ColorMode::tryFrom('random'));
        self::assertSame(ColorMode::THEME, ColorMode::tryFrom('theme'));
        self::assertSame(ColorMode::PAIRS, ColorMode::tryFrom('pairs'));
        self::assertSame(ColorMode::CUSTOM, ColorMode::tryFrom('custom'));
    }

    #[Test]
    public function colorModeTryFromReturnsNullForInvalidValue(): void
    {
        self::assertNull(ColorMode::tryFrom('invalid'));
        self::assertNull(ColorMode::tryFrom(''));
    }

    #[Test]
    public function imageDriverEnumHasCorrectValues(): void
    {
        self::assertSame('ImageMagick', ImageDriver::IMAGICK->value);
        self::assertSame('gd', ImageDriver::GD->value);
        self::assertSame('GraphicsMagick', ImageDriver::GMAGICK->value);
    }

    #[Test]
    public function imageDriverImplementsEnumInterface(): void
    {
        self::assertInstanceOf(EnumInterface::class, ImageDriver::IMAGICK);
        self::assertInstanceOf(\BackedEnum::class, ImageDriver::IMAGICK);
    }

    #[Test]
    public function imageFormatEnumHasCorrectValues(): void
    {
        self::assertSame('png', ImageFormat::PNG->value);
        self::assertSame('jpeg', ImageFormat::JPEG->value);
    }

    #[Test]
    public function imageFormatImplementsEnumInterface(): void
    {
        self::assertInstanceOf(EnumInterface::class, ImageFormat::PNG);
        self::assertInstanceOf(\BackedEnum::class, ImageFormat::PNG);
    }

    #[Test]
    public function shapeEnumHasCorrectValues(): void
    {
        self::assertSame('circle', Shape::CIRCLE->value);
        self::assertSame('square', Shape::SQUARE->value);
    }

    #[Test]
    public function shapeImplementsEnumInterface(): void
    {
        self::assertInstanceOf(EnumInterface::class, Shape::CIRCLE);
        self::assertInstanceOf(\BackedEnum::class, Shape::CIRCLE);
    }

    #[Test]
    public function transformEnumHasCorrectValues(): void
    {
        self::assertSame('none', Transform::NONE->value);
        self::assertSame('uppercase', Transform::UPPERCASE->value);
        self::assertSame('lowercase', Transform::LOWERCASE->value);
    }

    #[Test]
    public function transformImplementsEnumInterface(): void
    {
        self::assertInstanceOf(EnumInterface::class, Transform::NONE);
        self::assertInstanceOf(\BackedEnum::class, Transform::NONE);
    }

    #[Test]
    public function enumsHaveAllCasesMethod(): void
    {
        $colorModes = ColorMode::cases();
        self::assertCount(5, $colorModes);
        self::assertContains(ColorMode::STRINGIFY, $colorModes);
        self::assertContains(ColorMode::RANDOM, $colorModes);
        self::assertContains(ColorMode::THEME, $colorModes);
        self::assertContains(ColorMode::PAIRS, $colorModes);
        self::assertContains(ColorMode::CUSTOM, $colorModes);
    }

    #[Test]
    public function imageDriverTryFromWorksCorrectly(): void
    {
        self::assertSame(ImageDriver::IMAGICK, ImageDriver::tryFrom('ImageMagick'));
        self::assertSame(ImageDriver::GD, ImageDriver::tryFrom('gd'));
        self::assertSame(ImageDriver::GMAGICK, ImageDriver::tryFrom('GraphicsMagick'));
        self::assertNull(ImageDriver::tryFrom('invalid'));
    }

    #[Test]
    public function imageFormatTryFromWorksCorrectly(): void
    {
        self::assertSame(ImageFormat::PNG, ImageFormat::tryFrom('png'));
        self::assertSame(ImageFormat::JPEG, ImageFormat::tryFrom('jpeg'));
        self::assertNull(ImageFormat::tryFrom('gif'));
    }

    #[Test]
    public function shapeTryFromWorksCorrectly(): void
    {
        self::assertSame(Shape::CIRCLE, Shape::tryFrom('circle'));
        self::assertSame(Shape::SQUARE, Shape::tryFrom('square'));
        self::assertNull(Shape::tryFrom('triangle'));
    }

    #[Test]
    public function transformTryFromWorksCorrectly(): void
    {
        self::assertSame(Transform::NONE, Transform::tryFrom('none'));
        self::assertSame(Transform::UPPERCASE, Transform::tryFrom('uppercase'));
        self::assertSame(Transform::LOWERCASE, Transform::tryFrom('lowercase'));
        self::assertNull(Transform::tryFrom('capitalize'));
    }
}
