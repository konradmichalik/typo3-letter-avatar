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

namespace KonradMichalik\Typo3LetterAvatar\Utility;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\EnumInterface;
use ReflectionClass;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ConfigurationUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
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
        if (null === $value) {
            return null;
        }

        if (null !== $expectedEnumClass) {
            $enumClass = new ReflectionClass($expectedEnumClass);
            if ($enumClass->isSubclassOf(EnumInterface::class)
                && $enumClass->isEnum()
                && !($value instanceof $expectedEnumClass)
            ) {
                if (method_exists($expectedEnumClass, 'tryFrom')) {
                    return $expectedEnumClass::tryFrom($value);
                }
            }
        }

        return $value;
    }
}
