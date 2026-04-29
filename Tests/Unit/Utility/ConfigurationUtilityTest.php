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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Utility;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Utility\ConfigurationUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * ConfigurationUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class ConfigurationUtilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Simple TYPO3_CONF_VARS configuration without framework mocking
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]['configuration'] = [
            'size' => 100,
            'fontSize' => 0.5,
            'colorMode' => 'random',
            'imageFormat' => 'png',
            'stringValue' => 'test-string',
            'intValue' => 42,
            'floatValue' => 3.14,
            'boolValue' => true,
        ];
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][Configuration::EXT_KEY]);
        parent::tearDown();
    }

    #[Test]
    public function getMethodExists(): void
    {
        self::assertTrue(method_exists(ConfigurationUtility::class, 'get'));
    }

    #[Test]
    public function configurationUtilityHasCorrectMethodSignature(): void
    {
        $reflection = new \ReflectionClass(ConfigurationUtility::class);
        $method = $reflection->getMethod('get');

        self::assertTrue($method->isStatic());
        self::assertTrue($method->isPublic());

        $parameters = $method->getParameters();
        self::assertCount(2, $parameters);
        self::assertSame('key', $parameters[0]->getName());
        self::assertSame('expectedEnumClass', $parameters[1]->getName());
        self::assertTrue($parameters[1]->allowsNull());
    }

    #[Test]
    public function getIsStaticMethod(): void
    {
        $reflectionClass = new \ReflectionClass(ConfigurationUtility::class);
        $getMethod = $reflectionClass->getMethod('get');

        self::assertTrue($getMethod->isStatic());
    }
}
