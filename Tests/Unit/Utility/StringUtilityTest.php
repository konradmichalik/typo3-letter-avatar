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

    #[Test]
    public function resolveInitialsHandlesUmlautSurname(): void
    {
        // Demo fixture: editor.maria — Maria Müller
        $result = StringUtility::resolveInitials('Maria Müller', '', Transform::NONE);

        self::assertSame('MM', $result);
    }

    #[Test]
    public function resolveInitialsHandlesApostropheInName(): void
    {
        // Demo fixture: editor.thomas — Thomas O'Brien
        $result = StringUtility::resolveInitials("Thomas O'Brien", '', Transform::NONE);

        self::assertSame('TO', $result);
    }

    #[Test]
    public function resolveInitialsHandlesTwoCharFirstName(): void
    {
        // Demo fixture: editor.li — Li Wei
        $result = StringUtility::resolveInitials('Li Wei', '', Transform::NONE);

        self::assertSame('LW', $result);
    }

    #[Test]
    public function resolveInitialsTruncatesLongCompositeNamesToTwoLetters(): void
    {
        // Demo fixture: editor.long — Maximilian Hubertus von Habsburg-Lothringen
        $result = StringUtility::resolveInitials(
            'Maximilian Hubertus von Habsburg-Lothringen',
            '',
            Transform::NONE,
        );

        // Only first two name parts are used; result is fixed length 2
        self::assertSame('MH', $result);
        self::assertSame(2, mb_strlen($result));
    }

    #[Test]
    public function resolveInitialsHandlesUnicodeAcrossScripts(): void
    {
        // Demo fixture: editor.unicode — Émilie Łukasiewicz
        $result = StringUtility::resolveInitials('Émilie Łukasiewicz', '', Transform::NONE);

        self::assertSame('ÉŁ', $result);
    }

    #[Test]
    public function resolveInitialsHandlesUnicodeWithUppercaseTransform(): void
    {
        // Pre-uppercase Unicode characters should remain uppercase after transform
        $result = StringUtility::resolveInitials('émilie łukasiewicz', '', Transform::UPPERCASE);

        self::assertSame('ÉŁ', $result);
    }

    #[Test]
    public function resolveInitialsHandlesUmlautWithLowercaseTransform(): void
    {
        $result = StringUtility::resolveInitials('Maria Müller', '', Transform::LOWERCASE);

        self::assertSame('mm', $result);
    }

    #[Test]
    public function resolveInitialsCommaInverseOrderProducesSurnameInitialFirst(): void
    {
        // "Müller, Maria" → comma is split-token, so first part is "Müller"
        $result = StringUtility::resolveInitials('Müller, Maria', '', Transform::NONE);

        self::assertSame('MM', $result);
    }

    #[Test]
    public function resolveInitialsHandlesSingleUnicodeCharName(): void
    {
        $result = StringUtility::resolveInitials('Ł', '', Transform::NONE);

        self::assertSame('Ł', $result);
    }

    #[Test]
    public function resolveInitialsHandlesTabsAsWhitespace(): void
    {
        $result = StringUtility::resolveInitials("John\tDoe", '', Transform::NONE);

        self::assertSame('JD', $result);
    }

    #[Test]
    public function resolveInitialsHandlesNewlinesAsWhitespace(): void
    {
        $result = StringUtility::resolveInitials("John\nDoe", '', Transform::NONE);

        self::assertSame('JD', $result);
    }

    #[Test]
    public function resolveInitialsWithLongPreSetInitialsReturnsFullPreSetValue(): void
    {
        // Pre-set initials are passed through verbatim, no truncation
        $result = StringUtility::resolveInitials('John Doe', 'XYZ', Transform::NONE);

        self::assertSame('XYZ', $result);
    }

    #[Test]
    public function resolveInitialsWithEmptyPreSetFallsBackToName(): void
    {
        // Explicit empty pre-set must not short-circuit; name should be used
        $result = StringUtility::resolveInitials('Maria Müller', '', Transform::NONE);

        self::assertSame('MM', $result);
    }
}
