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

use function is_array;
use function is_string;

/**
 * BackendThemeResolver.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class BackendThemeResolver
{
    /**
     * @param array<string, mixed> $backendUser
     */
    public function resolveThemeName(array $backendUser): string
    {
        $mapping = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['backendThemes'] ?? [];
        if (!is_array($mapping) || [] === $mapping) {
            return '';
        }

        $settings = $this->extractUserSettings($backendUser);
        $scheme = $this->stringSetting($settings, 'colorScheme', 'auto');
        // "modern" mirrors TYPO3's default theme, so users who never touched the setup still get a proper accent.
        $theme = $this->stringSetting($settings, 'theme', 'modern');

        // Lookup priority: scheme:theme → theme → scheme → default
        foreach ([$scheme.':'.$theme, $theme, $scheme] as $key) {
            if (isset($mapping[$key])) {
                return (string) $mapping[$key];
            }
        }

        return (string) ($mapping['default'] ?? '');
    }

    /**
     * @param array<string, mixed> $settings
     */
    private function stringSetting(array $settings, string $key, string $default): string
    {
        $value = $settings[$key] ?? null;

        return is_string($value) && '' !== $value ? $value : $default;
    }

    /**
     * @param array<string, mixed> $backendUser
     *
     * @return array<string, mixed>
     */
    private function extractUserSettings(array $backendUser): array
    {
        // v14.2+ stores user settings as JSON in be_users.user_settings
        $userSettings = $backendUser['user_settings'] ?? null;
        if (is_string($userSettings) && '' !== $userSettings) {
            $decoded = json_decode($userSettings, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // v13 and v14 < 14.2 store as serialized array in be_users.uc
        $uc = $backendUser['uc'] ?? null;
        if (is_string($uc) && '' !== $uc) {
            $unserialized = @unserialize($uc, ['allowed_classes' => false]);
            if (is_array($unserialized)) {
                return $unserialized;
            }
        }

        return [];
    }
}
