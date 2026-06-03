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
use KonradMichalik\Typo3LetterAvatar\Service\BackendThemeResolver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BackendThemeResolverTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class BackendThemeResolverTest extends TestCase
{
    private BackendThemeResolver $resolver;

    protected function setUp(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'backendThemes' => [
                // theme-specific (primary axis)
                'modern' => 'backend-modern',
                'fresh' => 'backend-fresh',
                'classic' => 'backend-classic',
                // scheme fallback (only used when theme is unknown)
                'light' => 'grayscale-light',
                'dark' => 'grayscale-dark',
                'auto' => 'grayscale-light',
                'default' => 'backend-modern',
            ],
        ];

        $this->resolver = new BackendThemeResolver();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
    }

    #[Test]
    public function resolveThemeNameReadsThemeFromJsonUserSettings(): void
    {
        $backendUser = [
            'user_settings' => json_encode(['colorScheme' => 'dark', 'theme' => 'fresh']),
        ];

        self::assertSame('backend-fresh', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNameReadsThemeFromSerializedUc(): void
    {
        $backendUser = [
            'uc' => serialize(['colorScheme' => 'light', 'theme' => 'classic']),
        ];

        self::assertSame('backend-classic', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNamePrefersJsonOverUcWhenBothPresent(): void
    {
        $backendUser = [
            'user_settings' => json_encode(['theme' => 'fresh']),
            'uc' => serialize(['theme' => 'classic']),
        ];

        self::assertSame('backend-fresh', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNameTreatsMissingThemeAsModern(): void
    {
        // TYPO3 default theme is "modern" — users who never visited the setup get this implicitly.
        $backendUser = [
            'user_settings' => json_encode(['colorScheme' => 'dark']),
        ];

        self::assertSame('backend-modern', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNameTreatsEmptyBackendUserAsModern(): void
    {
        self::assertSame('backend-modern', $this->resolver->resolveThemeName([]));
    }

    #[Test]
    public function resolveThemeNameUsesSchemeFallbackForUnknownTheme(): void
    {
        // A future TYPO3 theme value the mapping doesn't know about.
        $backendUser = [
            'user_settings' => json_encode(['colorScheme' => 'dark', 'theme' => 'midnight']),
        ];

        self::assertSame('grayscale-dark', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNameFallsBackToDefaultForUnknownSchemeAndTheme(): void
    {
        $backendUser = [
            'user_settings' => json_encode(['colorScheme' => 'sepia', 'theme' => 'midnight']),
        ];

        self::assertSame('backend-modern', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNameReturnsEmptyStringWhenMappingIsMissing(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [];

        self::assertSame('', $this->resolver->resolveThemeName(['user_settings' => json_encode(['colorScheme' => 'dark'])]));
    }

    #[Test]
    public function resolveThemeNameReturnsEmptyStringWhenSchemeUnknownAndNoDefault(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'backendThemes' => [
                'light' => 'grayscale-light',
            ],
        ];

        self::assertSame('', $this->resolver->resolveThemeName(['user_settings' => json_encode(['colorScheme' => 'dark'])]));
    }

    #[Test]
    public function resolveThemeNameHandlesMalformedJsonGracefully(): void
    {
        $backendUser = [
            'user_settings' => '{ not valid json',
        ];

        // Falls back to the implicit "modern" default (no exception).
        self::assertSame('backend-modern', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNameHandlesMalformedSerializedUcGracefully(): void
    {
        $backendUser = [
            'uc' => 'not-a-valid-serialized-string',
        ];

        self::assertSame('backend-modern', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNameUsesThemePaletteRegardlessOfColorScheme(): void
    {
        $backendUser = [
            'user_settings' => json_encode(['colorScheme' => 'dark', 'theme' => 'classic']),
        ];

        self::assertSame('backend-classic', $this->resolver->resolveThemeName($backendUser));
    }

    #[Test]
    public function resolveThemeNamePrefersCompositeKeyOverThemeKey(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['backendThemes'] = [
            'dark:fresh' => 'custom-dark-fresh',
            'fresh' => 'backend-fresh',
            'default' => 'grayscale-light',
        ];

        $backendUser = [
            'user_settings' => json_encode(['colorScheme' => 'dark', 'theme' => 'fresh']),
        ];

        self::assertSame('custom-dark-fresh', $this->resolver->resolveThemeName($backendUser));
    }
}
