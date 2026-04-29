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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * ConfigurationTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class ConfigurationTest extends TestCase
{
    #[Test]
    public function extKeyConstantIsCorrect(): void
    {
        self::assertSame('typo3_letter_avatar', Configuration::EXT_KEY);
    }

    #[Test]
    public function extNameConstantIsCorrect(): void
    {
        self::assertSame('Typo3LetterAvatar', Configuration::EXT_NAME);
    }

    #[Test]
    public function pluginNameConstantIsCorrect(): void
    {
        self::assertSame('FrontendEdit', Configuration::PLUGIN_NAME);
    }

    #[Test]
    public function typeConstantIsCorrect(): void
    {
        self::assertSame('1729341864', Configuration::TYPE);
    }

    #[Test]
    public function constantsAreStrings(): void
    {
        self::assertIsString(Configuration::EXT_KEY);
        self::assertIsString(Configuration::EXT_NAME);
        self::assertIsString(Configuration::PLUGIN_NAME);
        self::assertIsString(Configuration::TYPE);
    }

    #[Test]
    public function constantsAreNotEmpty(): void
    {
        self::assertNotEmpty(Configuration::EXT_KEY);
        self::assertNotEmpty(Configuration::EXT_NAME);
        self::assertNotEmpty(Configuration::PLUGIN_NAME);
        self::assertNotEmpty(Configuration::TYPE);
    }
}
