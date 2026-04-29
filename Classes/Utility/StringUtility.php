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

use KonradMichalik\Typo3LetterAvatar\Enum\Transform;

/**
 * StringUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class StringUtility
{
    public static function resolveInitials(string $name, string $preSetInitials = '', Transform $transform = Transform::NONE): string
    {
        if ('' !== $preSetInitials) {
            return self::applyTransform($preSetInitials, $transform);
        }

        $nameParts = self::splitName($name);
        if ([] === $nameParts) {
            return '';
        }

        $initials = self::extractFirstLetter($nameParts[0]);
        if (isset($nameParts[1])) {
            $initials .= self::extractFirstLetter($nameParts[1]);
        }

        return self::applyTransform($initials, $transform);
    }

    protected static function applyTransform(string $string, Transform $transform): string
    {
        return match ($transform) {
            Transform::UPPERCASE => mb_strtoupper($string),
            Transform::LOWERCASE => mb_strtolower($string),
            default => $string,
        };
    }

    protected static function extractFirstLetter(string $word): string
    {
        return mb_strtoupper(mb_substr(trim($word), 0, 1, 'UTF-8'));
    }

    protected static function splitName(string $name): array
    {
        // Remove multiple spaces and split by single space
        $cleaned = preg_replace('/\s+/', ' ', trim($name));

        return array_filter(explode(' ', $cleaned), static fn (string $word): bool => '' !== $word && ',' !== $word);
    }
}
