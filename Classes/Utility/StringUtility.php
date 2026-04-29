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

namespace KonradMichalik\Typo3LetterAvatar\Utility;

use KonradMichalik\Typo3LetterAvatar\Enum\Transform;

/**
 * StringUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
class StringUtility
{
    public static function resolveInitials(string $name, string $preSetInitials = '', Transform $transform = Transform::NONE): string
    {
        if ($preSetInitials !== '') {
            return self::applyTransform($preSetInitials, $transform);
        }

        $nameParts = self::splitName($name);
        if ($nameParts === []) {
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
        return array_filter(explode(' ', $cleaned), fn(string $word): bool => $word !== '' && $word !== ',');
    }
}
