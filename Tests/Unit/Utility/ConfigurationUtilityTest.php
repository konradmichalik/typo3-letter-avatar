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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Utility;

use KonradMichalik\Ttt\Attribute\WithTypo3ConfVars;
use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Utility\ConfigurationUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * ConfigurationUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
#[WithTypo3ConfVars([
    'EXTENSIONS' => [Configuration::EXT_KEY => []],
    'EXTCONF' => [Configuration::EXT_KEY => ['configuration' => [
        'size' => 100,
        'fontSize' => 0.5,
        'colorMode' => 'random',
        'imageFormat' => 'png',
        'stringValue' => 'test-string',
        'intValue' => 42,
        'floatValue' => 3.14,
        'boolValue' => true,
    ]]],
])]
final class ConfigurationUtilityTest extends TestCase
{
    #[Test]
    public function getReturnsConfiguredScalarValues(): void
    {
        self::assertSame(100, ConfigurationUtility::get('size'));
        self::assertSame('test-string', ConfigurationUtility::get('stringValue'));
        self::assertTrue(ConfigurationUtility::get('boolValue'));
    }

    #[Test]
    public function getReturnsNullForUnknownKey(): void
    {
        self::assertNull(ConfigurationUtility::get('doesNotExist'));
    }

    #[Test]
    public function getCastsValueToRequestedEnum(): void
    {
        self::assertSame(ColorMode::RANDOM, ConfigurationUtility::get('colorMode', ColorMode::class));
    }

    #[Test]
    public function getReturnsNullForInvalidEnumValue(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['colorMode'] = 'not-a-mode';

        self::assertNull(ConfigurationUtility::get('colorMode', ColorMode::class));
    }

    #[Test]
    public function getReflectsConfigurationChangesImmediately(): void
    {
        self::assertSame(100, ConfigurationUtility::get('size'));

        // The live configuration is read on every call, so changes are visible at once.
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration']['size'] = 200;

        self::assertSame(200, ConfigurationUtility::get('size'));
    }
}
