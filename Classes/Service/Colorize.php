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

namespace KonradMichalik\Typo3LetterAvatar\Service;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Image\AbstractImageProvider;
use KonradMichalik\Typo3LetterAvatar\Utility\{ConfigurationUtility, StringUtility};

use function sprintf;

/**
 * Colorize.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Colorize
{
    private array $foregroundColors = [];
    private array $backgroundColors = [];

    public function __construct(protected AbstractImageProvider $avatar) {}

    public function resolveForegroundColor(): string
    {
        return match ($this->avatar->mode) {
            ColorMode::CUSTOM => $this->avatar->foregroundColor,
            ColorMode::STRINGIFY, ColorMode::RANDOM => $this->getRandomForegroundColor(),
            ColorMode::THEME, ColorMode::BACKEND_THEME => $this->getRandomThemeFrontendColor(),
            ColorMode::PAIRS => $this->getPairFrontendColor(),
        };
    }

    public function resolveBackgroundColor(): string
    {
        return match ($this->avatar->mode) {
            ColorMode::CUSTOM => $this->avatar->backgroundColor,
            ColorMode::STRINGIFY => $this->stringToColor(
                StringUtility::resolveInitials($this->avatar->name, $this->avatar->initials, $this->avatar->transform),
            ),
            ColorMode::RANDOM => $this->getRandomBackgroundColor(),
            ColorMode::THEME, ColorMode::BACKEND_THEME => $this->getRandomThemeBackendColor(),
            ColorMode::PAIRS => $this->getPairBackgroundColor(),
        };
    }

    private function getPairFrontendColor(): string
    {
        $this->initializePairColors();

        return $this->foregroundColors[array_rand($this->foregroundColors)];
    }

    private function getPairBackgroundColor(): string
    {
        $this->initializePairColors();

        return $this->backgroundColors[array_rand($this->backgroundColors)];
    }

    private function initializePairColors(): void
    {
        if ([] === $this->foregroundColors || [] === $this->backgroundColors) {
            $pairColors = $this->getConfigRandom('pairs');
            $this->foregroundColors = [$pairColors['foreground']];
            $this->backgroundColors = [$pairColors['background']];
        }
    }

    private function getRandomForegroundColor(): string
    {
        $this->initializeRandomColors();

        return $this->foregroundColors[array_rand($this->foregroundColors)];
    }

    private function getRandomBackgroundColor(): string
    {
        $this->initializeRandomColors();

        return $this->backgroundColors[array_rand($this->backgroundColors)];
    }

    private function initializeRandomColors(): void
    {
        if ([] === $this->foregroundColors || [] === $this->backgroundColors) {
            $randomConfig = $this->getRandomConfig();
            $this->foregroundColors = $randomConfig['foregrounds'];
            $this->backgroundColors = $randomConfig['backgrounds'];
        }
    }

    private function getRandomThemeFrontendColor(): string
    {
        $this->initializeThemeColors();

        return $this->foregroundColors[array_rand($this->foregroundColors)];
    }

    private function getRandomThemeBackendColor(): string
    {
        $this->initializeThemeColors();

        return $this->backgroundColors[array_rand($this->backgroundColors)];
    }

    private function initializeThemeColors(): void
    {
        if ([] === $this->foregroundColors || [] === $this->backgroundColors) {
            // Prefer the per-call theme (e.g. BACKEND_THEME resolved per user);
            // fall back to the global extension setting.
            $themes = '' !== $this->avatar->theme
                ? [$this->avatar->theme]
                : (array) ConfigurationUtility::get('theme');
            foreach ($themes as $theme) {
                $themeConfig = $this->getThemeConfig($theme);
                $this->foregroundColors = [...$this->foregroundColors, ...($themeConfig['foregrounds'] ?? [])];
                $this->backgroundColors = [...$this->backgroundColors, ...($themeConfig['backgrounds'] ?? [])];
            }
        }
    }

    private function stringToColor(string $string): string
    {
        $rgb = substr(hash('crc32b', $string), 0, 6);

        return sprintf(
            '#%02X%02X%02X',
            hexdec(substr($rgb, 0, 2)) / 2,
            hexdec(substr($rgb, 2, 2)) / 2,
            hexdec(substr($rgb, 4, 2)) / 2,
        );
    }

    private function getRandomConfig(): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['random'];
    }

    private function getConfigRandom(string $key): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'][$key][array_rand($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'][$key])];
    }

    private function getThemeConfig(string $theme): array
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['themes'][$theme] ?? [];
    }
}
