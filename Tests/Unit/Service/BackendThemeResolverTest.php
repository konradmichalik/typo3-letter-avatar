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

use KonradMichalik\Ttt\Attribute\{Typo3ConfVarsSentinel, WithTypo3ConfVars};
use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Service\BackendThemeResolver;
use PHPUnit\Framework\Attributes\{DataProvider, Test};
use PHPUnit\Framework\TestCase;

/**
 * BackendThemeResolverTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['configuration' => [
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
]]]])]
final class BackendThemeResolverTest extends TestCase
{
    /**
     * @return array<string, array{array<string, string>, string}>
     */
    public static function backendUserProvider(): array
    {
        return [
            'theme from JSON user settings' => [self::userSettings(['colorScheme' => 'dark', 'theme' => 'fresh']), 'backend-fresh'],
            'theme from serialized uc' => [['uc' => serialize(['colorScheme' => 'light', 'theme' => 'classic'])], 'backend-classic'],
            'JSON wins over uc' => [[...self::userSettings(['theme' => 'fresh']), 'uc' => serialize(['theme' => 'classic'])], 'backend-fresh'],
            // TYPO3's default theme is "modern", users who never visited the setup get it implicitly
            'missing theme counts as modern' => [self::userSettings(['colorScheme' => 'dark']), 'backend-modern'],
            'empty backend user counts as modern' => [[], 'backend-modern'],
            'unknown theme falls back to scheme' => [self::userSettings(['colorScheme' => 'dark', 'theme' => 'midnight']), 'grayscale-dark'],
            'unknown scheme and theme fall back to default' => [self::userSettings(['colorScheme' => 'sepia', 'theme' => 'midnight']), 'backend-modern'],
            'malformed JSON is ignored' => [['user_settings' => '{ not valid json'], 'backend-modern'],
            'malformed uc is ignored' => [['uc' => 'not-a-valid-serialized-string'], 'backend-modern'],
        ];
    }

    /**
     * @param array<string, string> $backendUser
     */
    #[Test]
    #[DataProvider('backendUserProvider')]
    public function resolveThemeName(array $backendUser, string $expected): void
    {
        self::assertSame($expected, (new BackendThemeResolver())->resolveThemeName($backendUser));
    }

    #[Test]
    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['configuration' => ['backendThemes' => ['dark:fresh' => 'custom-dark-fresh']]]]])]
    public function resolveThemeNamePrefersCompositeKeyOverThemeKey(): void
    {
        $backendUser = self::userSettings(['colorScheme' => 'dark', 'theme' => 'fresh']);

        self::assertSame('custom-dark-fresh', (new BackendThemeResolver())->resolveThemeName($backendUser));
    }

    #[Test]
    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['configuration' => ['backendThemes' => Typo3ConfVarsSentinel::Unset]]]])]
    public function resolveThemeNameReturnsEmptyStringWithoutMapping(): void
    {
        self::assertSame('', (new BackendThemeResolver())->resolveThemeName(self::userSettings(['colorScheme' => 'dark'])));
    }

    #[Test]
    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['configuration' => ['backendThemes' => Typo3ConfVarsSentinel::Unset]]]])]
    #[WithTypo3ConfVars(['EXTCONF' => [Configuration::EXT_KEY => ['configuration' => ['backendThemes' => ['light' => 'grayscale-light']]]]])]
    public function resolveThemeNameReturnsEmptyStringWhenNothingMatchesAndNoDefault(): void
    {
        self::assertSame('', (new BackendThemeResolver())->resolveThemeName(self::userSettings(['colorScheme' => 'dark'])));
    }

    /**
     * @param array<string, string> $settings
     *
     * @return array{user_settings: string}
     */
    private static function userSettings(array $settings): array
    {
        return ['user_settings' => json_encode($settings, \JSON_THROW_ON_ERROR)];
    }
}
