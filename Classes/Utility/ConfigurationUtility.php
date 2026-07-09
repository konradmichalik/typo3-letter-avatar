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
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

use function is_int;
use function is_string;
use function is_subclass_of;

/**
 * ConfigurationUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class ConfigurationUtility
{
    private static ?ExtensionConfiguration $extensionConfiguration = null;

    public static function get(string $key, ?string $expectedEnumClass = null): array|string|int|float|bool|EnumInterface|null
    {
        $value = self::getMergedConfiguration()[$key] ?? null;
        if (null === $value) {
            return null;
        }

        if (null !== $expectedEnumClass && is_subclass_of($expectedEnumClass, EnumInterface::class)) {
            if ($value instanceof $expectedEnumClass) {
                return $value;
            }

            // EnumInterface extends BackedEnum, so tryFrom() is guaranteed here.
            return (is_string($value) || is_int($value)) ? $expectedEnumClass::tryFrom($value) : null;
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private static function getMergedConfiguration(): array
    {
        // The ExtensionConfiguration service is stateless and reads the live
        // configuration on each call, so caching the instance avoids repeated
        // service instantiation without ever serving stale configuration.
        self::$extensionConfiguration ??= GeneralUtility::makeInstance(ExtensionConfiguration::class);

        return array_merge(
            self::$extensionConfiguration->get(Configuration::EXT_KEY),
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] ?? [],
        );
    }
}
