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
use PHPUnit\Framework\Attributes\{DataProvider, Test};
use PHPUnit\Framework\TestCase;

/**
 * StringUtilityTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class StringUtilityTest extends TestCase
{
    /**
     * @return array<string, array{string, string, Transform, string}>
     */
    public static function initialsProvider(): array
    {
        return [
            'pre-set initials are returned verbatim' => ['John Doe', 'XYZ', Transform::NONE, 'XYZ'],
            'pre-set initials are uppercased' => ['John Doe', 'xy', Transform::UPPERCASE, 'XY'],
            'pre-set initials are lowercased' => ['John Doe', 'XY', Transform::LOWERCASE, 'xy'],
            'single name' => ['John', '', Transform::NONE, 'J'],
            'first and last name' => ['John Doe', '', Transform::NONE, 'JD'],
            'only the first two name parts count' => ['Maximilian Hubertus von Habsburg-Lothringen', '', Transform::NONE, 'MH'],
            'lowercase name is uppercased' => ['john doe', '', Transform::NONE, 'JD'],
            'empty name' => ['', '', Transform::NONE, ''],
            'whitespace-only name' => ['   ', '', Transform::NONE, ''],
            'surrounding and repeated whitespace' => ['  John   Doe  ', '', Transform::NONE, 'JD'],
            'tabs and newlines separate name parts' => ["John\t\nDoe", '', Transform::NONE, 'JD'],
            'surname before comma' => ['Doe, John', '', Transform::NONE, 'DJ'],
            'hyphenated first name' => ['John-Paul Smith', '', Transform::NONE, 'JS'],
            'apostrophe in surname' => ["Thomas O'Brien", '', Transform::NONE, 'TO'],
            'non-latin initials' => ['Émilie Łukasiewicz', '', Transform::NONE, 'ÉŁ'],
            'non-latin initials lowercased' => ['Émilie Łukasiewicz', '', Transform::LOWERCASE, 'éł'],
            'single non-latin letter' => ['Ł', '', Transform::NONE, 'Ł'],
            'emoji as first character' => ['🚀 John Doe', '', Transform::NONE, '🚀J'],
        ];
    }

    #[Test]
    #[DataProvider('initialsProvider')]
    public function resolveInitials(string $name, string $preSetInitials, Transform $transform, string $expected): void
    {
        self::assertSame($expected, StringUtility::resolveInitials($name, $preSetInitials, $transform));
    }
}
