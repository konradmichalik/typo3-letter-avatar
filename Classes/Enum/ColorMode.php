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

namespace KonradMichalik\Typo3LetterAvatar\Enum;

/**
 * ColorMode.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
enum ColorMode: string implements EnumInterface
{
    case CUSTOM = 'custom';
    case STRINGIFY = 'stringify';
    case RANDOM = 'random';
    case THEME = 'theme';
    case PAIRS = 'pairs';
}
