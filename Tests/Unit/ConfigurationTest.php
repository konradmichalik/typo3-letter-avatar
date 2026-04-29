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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * ConfigurationTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
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
