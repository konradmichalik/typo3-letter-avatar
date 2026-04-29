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

use KonradMichalik\Typo3LetterAvatar\Enum\Transform;
use KonradMichalik\Typo3LetterAvatar\Utility\StringUtility;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * StringUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class StringUtilityTest extends TestCase
{
    #[Test]
    public function resolveInitialsWithPreSetInitialsReturnsPreSetValue(): void
    {
        $result = StringUtility::resolveInitials('John Doe', 'XY', Transform::NONE);

        self::assertSame('XY', $result);
    }

    #[Test]
    public function resolveInitialsWithSingleNameReturnsFirstLetter(): void
    {
        $result = StringUtility::resolveInitials('John', '', Transform::NONE);

        self::assertSame('J', $result);
    }

    #[Test]
    public function resolveInitialsWithFullNameReturnsTwoInitials(): void
    {
        $result = StringUtility::resolveInitials('John Doe', '', Transform::NONE);

        self::assertSame('JD', $result);
    }

    #[Test]
    public function resolveInitialsWithMultipleNamesReturnsTwoInitials(): void
    {
        $result = StringUtility::resolveInitials('John Michael Doe', '', Transform::NONE);

        self::assertSame('JM', $result);
    }

    #[Test]
    public function resolveInitialsWithEmptyNameReturnsEmptyString(): void
    {
        $result = StringUtility::resolveInitials('', '', Transform::NONE);

        self::assertSame('', $result);
    }

    #[Test]
    public function resolveInitialsWithOnlySpacesReturnsEmptyString(): void
    {
        $result = StringUtility::resolveInitials('   ', '', Transform::NONE);

        self::assertSame('', $result);
    }

    #[Test]
    public function resolveInitialsTrimsWhitespace(): void
    {
        $result = StringUtility::resolveInitials('  John   Doe  ', '', Transform::NONE);

        self::assertSame('JD', $result);
    }

    #[Test]
    public function resolveInitialsIgnoresCommas(): void
    {
        $result = StringUtility::resolveInitials('Doe, John', '', Transform::NONE);

        self::assertSame('DJ', $result);
    }

    #[Test]
    public function resolveInitialsWithUppercaseTransform(): void
    {
        $result = StringUtility::resolveInitials('john doe', '', Transform::UPPERCASE);

        self::assertSame('JD', $result);
    }

    #[Test]
    public function resolveInitialsWithLowercaseTransform(): void
    {
        $result = StringUtility::resolveInitials('JOHN DOE', '', Transform::LOWERCASE);

        self::assertSame('jd', $result);
    }

    #[Test]
    public function resolveInitialsWithUnicodeCharacters(): void
    {
        $result = StringUtility::resolveInitials('Jürgen Müller', '', Transform::NONE);

        self::assertSame('JM', $result);
    }

    #[Test]
    public function resolveInitialsWithPreSetInitialsAppliesTransform(): void
    {
        $result = StringUtility::resolveInitials('John Doe', 'xy', Transform::UPPERCASE);

        self::assertSame('XY', $result);
    }

    #[Test]
    public function resolveInitialsWithPreSetInitialsLowercaseTransform(): void
    {
        $result = StringUtility::resolveInitials('John Doe', 'XY', Transform::LOWERCASE);

        self::assertSame('xy', $result);
    }

    #[Test]
    public function resolveInitialsWithEmojiHandling(): void
    {
        $result = StringUtility::resolveInitials('🚀 John Doe', '', Transform::NONE);

        // Should extract "🚀" and "J" as initials since emoji is treated as first character
        self::assertSame('🚀J', $result);
    }

    #[Test]
    public function resolveInitialsWithSpecialCharacters(): void
    {
        $result = StringUtility::resolveInitials('John-Paul Smith', '', Transform::NONE);

        self::assertSame('JS', $result);
    }
}
