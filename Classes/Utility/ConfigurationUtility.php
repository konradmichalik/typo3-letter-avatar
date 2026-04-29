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

namespace KonradMichalik\Typo3LetterAvatar\Utility;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\EnumInterface;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ConfigurationUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
class ConfigurationUtility
{
    public static function get(string $key, ?string $expectedEnumClass = null): array|string|int|float|bool|EnumInterface|null
    {
        $configuration = array_merge(
            GeneralUtility::makeInstance(ExtensionConfiguration::class)->get(Configuration::EXT_KEY),
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] ?? [],
        );

        $value = $configuration[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if ($expectedEnumClass !== null) {
            $enumClass = new \ReflectionClass($expectedEnumClass);
            if ($enumClass->isSubclassOf(EnumInterface::class) &&
                $enumClass->isEnum() &&
                !($value instanceof $expectedEnumClass)
            ) {
                if (method_exists($expectedEnumClass, 'tryFrom')) {
                    return $expectedEnumClass::tryFrom($value);
                }
            }
        }

        return $value;
    }
}
