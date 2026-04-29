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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Service;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Image\AbstractImageProvider;
use KonradMichalik\Typo3LetterAvatar\Service\Colorize;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * ColorizeTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class ColorizeTest extends TestCase
{
    private AbstractImageProvider $avatarProvider;
    private Colorize $colorizeService;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up mock configuration for testing
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'random' => [
                'foregrounds' => ['#FFFFFF', '#000000'],
                'backgrounds' => ['#FF0000', '#00FF00', '#0000FF'],
            ],
            'pairs' => [
                [
                    'foreground' => '#FFFFFF',
                    'background' => '#000000',
                ],
                [
                    'foreground' => '#000000',
                    'background' => '#FFFFFF',
                ],
            ],
            'themes' => [
                'test-theme' => [
                    'foregrounds' => ['#CCCCCC'],
                    'backgrounds' => ['#333333'],
                ],
                'multi-theme' => [
                    'foregrounds' => ['#AAA', '#BBB'],
                    'backgrounds' => ['#111', '#222'],
                ],
            ],
        ];

        $this->avatarProvider = $this->createMock(AbstractImageProvider::class);
        $this->colorizeService = new Colorize($this->avatarProvider);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
        parent::tearDown();
    }

    #[Test]
    public function resolveForegroundColorWithCustomModeReturnsAvatarForegroundColor(): void
    {
        $this->avatarProvider->mode = ColorMode::CUSTOM;
        $this->avatarProvider->foregroundColor = '#ABCDEF';

        $result = $this->colorizeService->resolveForegroundColor();

        self::assertSame('#ABCDEF', $result);
    }

    #[Test]
    public function resolveBackgroundColorWithCustomModeReturnsAvatarBackgroundColor(): void
    {
        $this->avatarProvider->mode = ColorMode::CUSTOM;
        $this->avatarProvider->backgroundColor = '#123456';

        $result = $this->colorizeService->resolveBackgroundColor();

        self::assertSame('#123456', $result);
    }

    #[Test]
    public function resolveForegroundColorWithRandomModeReturnsValidColor(): void
    {
        $this->avatarProvider->mode = ColorMode::RANDOM;

        $result = $this->colorizeService->resolveForegroundColor();

        self::assertContains($result, ['#FFFFFF', '#000000']);
    }

    #[Test]
    public function resolveBackgroundColorWithRandomModeReturnsValidColor(): void
    {
        $this->avatarProvider->mode = ColorMode::RANDOM;

        $result = $this->colorizeService->resolveBackgroundColor();

        self::assertContains($result, ['#FF0000', '#00FF00', '#0000FF']);
    }

    #[Test]
    public function resolveForegroundColorWithPairsModeReturnsValidColor(): void
    {
        $this->avatarProvider->mode = ColorMode::PAIRS;

        $result = $this->colorizeService->resolveForegroundColor();

        self::assertContains($result, ['#FFFFFF', '#000000']);
    }

    #[Test]
    public function resolveBackgroundColorWithPairsModeReturnsValidColor(): void
    {
        $this->avatarProvider->mode = ColorMode::PAIRS;

        $result = $this->colorizeService->resolveBackgroundColor();

        self::assertContains($result, ['#000000', '#FFFFFF']);
    }

    #[Test]
    public function resolveBackgroundColorWithStringifyModeReturnsConsistentColor(): void
    {
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->name = 'John Doe';
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $result1 = $this->colorizeService->resolveBackgroundColor();
        $result2 = $this->colorizeService->resolveBackgroundColor();

        // Should return the same color for the same input
        self::assertSame($result1, $result2);
        // Should be a valid hex color
        self::assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $result1);
    }

    #[Test]
    public function resolveBackgroundColorWithStringifyModeReturnsDifferentColorsForDifferentNames(): void
    {
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $this->avatarProvider->name = 'John Doe';
        $color1 = $this->colorizeService->resolveBackgroundColor();

        $this->avatarProvider->name = 'Jane Smith';
        $color2 = $this->colorizeService->resolveBackgroundColor();

        self::assertNotSame($color1, $color2);
    }

    #[Test]
    public function colorsAreConsistentAcrossMultipleCalls(): void
    {
        $this->avatarProvider->mode = ColorMode::PAIRS;

        // First call should initialize colors
        $fg1 = $this->colorizeService->resolveForegroundColor();
        $bg1 = $this->colorizeService->resolveBackgroundColor();

        // Subsequent calls should return the same colors (cached)
        $fg2 = $this->colorizeService->resolveForegroundColor();
        $bg2 = $this->colorizeService->resolveBackgroundColor();

        self::assertSame($fg1, $fg2);
        self::assertSame($bg1, $bg2);
    }
}
