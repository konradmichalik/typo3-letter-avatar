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

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\{GeneralUtility, PathUtility as CorePathUtility};

/**
 * PathUtility.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class PathUtility
{
    public static function getImageFolder(): string
    {
        $folder = Environment::getPublicPath().ConfigurationUtility::get('imagePath');

        if (!str_ends_with($folder, '/')) {
            $folder .= '/';
        }

        if (!is_dir($folder)) {
            GeneralUtility::mkdir_deep($folder);
        }

        return $folder;
    }

    public static function getWebPath(string $filename): string
    {
        return CorePathUtility::getAbsoluteWebPath(ConfigurationUtility::get('imagePath').$filename);
    }
}
