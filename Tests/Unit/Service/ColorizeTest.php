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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Service;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Image\AbstractImageProvider;
use KonradMichalik\Typo3LetterAvatar\Service\Colorize;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function count;

/**
 * ColorizeTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ColorizeTest extends TestCase
{
    private AbstractImageProvider $avatarProvider;
    private Colorize $colorizeService;

    protected function setUp(): void
    {
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

    #[Test]
    public function resolveBackgroundColorWithStringifyHandlesUmlautName(): void
    {
        // Demo fixture: Maria Müller — must produce stable hex color
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->name = 'Maria Müller';
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $color = $this->colorizeService->resolveBackgroundColor();

        self::assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $color);
    }

    #[Test]
    public function resolveBackgroundColorWithStringifyHandlesApostropheName(): void
    {
        // Demo fixture: Thomas O'Brien
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->name = "Thomas O'Brien";
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $color = $this->colorizeService->resolveBackgroundColor();

        self::assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $color);
    }

    #[Test]
    public function resolveBackgroundColorWithStringifyHandlesUnicodeName(): void
    {
        // Demo fixture: Émilie Łukasiewicz
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->name = 'Émilie Łukasiewicz';
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $color = $this->colorizeService->resolveBackgroundColor();

        self::assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $color);
    }

    #[Test]
    public function resolveBackgroundColorWithStringifyHandlesLongCompositeName(): void
    {
        // Demo fixture: Maximilian Hubertus von Habsburg-Lothringen
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->name = 'Maximilian Hubertus von Habsburg-Lothringen';
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $color = $this->colorizeService->resolveBackgroundColor();

        self::assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $color);
    }

    #[Test]
    public function resolveBackgroundColorWithStringifyHandlesTwoCharFirstName(): void
    {
        // Demo fixture: Li Wei
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->name = 'Li Wei';
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $color = $this->colorizeService->resolveBackgroundColor();

        self::assertMatchesRegularExpression('/^#[0-9A-F]{6}$/i', $color);
    }

    #[Test]
    public function resolveBackgroundColorWithStringifyAllDemoFixturesProduceUniqueColors(): void
    {
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $names = [
            'Maria Müller',
            "Thomas O'Brien",
            'Li Wei',
            'Maximilian Hubertus von Habsburg-Lothringen',
            'Émilie Łukasiewicz',
        ];

        $colors = [];
        foreach ($names as $name) {
            $this->avatarProvider->name = $name;
            // Reinstantiate so cached colors don't leak between names
            $service = new Colorize($this->avatarProvider);
            $colors[] = $service->resolveBackgroundColor();
        }

        // All produced hex colors should be unique (CRC32-based hash collision-free for these inputs)
        self::assertCount(count($names), array_unique($colors));
    }

    #[Test]
    public function resolveBackgroundColorWithStringifyDarkensColorByHalving(): void
    {
        // The implementation halves each RGB component (hexdec/2). Verify each channel is in [0, 127].
        $this->avatarProvider->mode = ColorMode::STRINGIFY;
        $this->avatarProvider->name = 'John Doe';
        $this->avatarProvider->initials = '';
        $this->avatarProvider->transform = \KonradMichalik\Typo3LetterAvatar\Enum\Transform::NONE;

        $color = $this->colorizeService->resolveBackgroundColor();

        self::assertSame('#', $color[0]);
        $r = hexdec(substr($color, 1, 2));
        $g = hexdec(substr($color, 3, 2));
        $b = hexdec(substr($color, 5, 2));

        self::assertLessThanOrEqual(127, $r);
        self::assertLessThanOrEqual(127, $g);
        self::assertLessThanOrEqual(127, $b);
    }

    #[Test]
    public function resolveForegroundColorWithStringifyDelegatesToRandomColors(): void
    {
        // ColorMode::STRINGIFY uses random foreground colors (only background is hashed)
        $this->avatarProvider->mode = ColorMode::STRINGIFY;

        $result = $this->colorizeService->resolveForegroundColor();

        self::assertContains($result, ['#FFFFFF', '#000000']);
    }

    #[Test]
    public function resolveBackgroundColorWithThemeUsesPerAvatarThemeWhenSet(): void
    {
        $this->avatarProvider->mode = ColorMode::THEME;
        $this->avatarProvider->theme = 'test-theme';

        $result = $this->colorizeService->resolveBackgroundColor();

        self::assertSame('#333333', $result);
    }

    #[Test]
    public function resolveBackgroundColorWithBackendThemeUsesPerAvatarTheme(): void
    {
        $this->avatarProvider->mode = ColorMode::BACKEND_THEME;
        $this->avatarProvider->theme = 'test-theme';

        $result = $this->colorizeService->resolveBackgroundColor();

        self::assertSame('#333333', $result);
    }

    #[Test]
    public function resolveForegroundColorWithBackendThemeUsesPerAvatarTheme(): void
    {
        $this->avatarProvider->mode = ColorMode::BACKEND_THEME;
        $this->avatarProvider->theme = 'test-theme';

        $result = $this->colorizeService->resolveForegroundColor();

        self::assertSame('#CCCCCC', $result);
    }
}
