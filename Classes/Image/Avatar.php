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

namespace KonradMichalik\Typo3LetterAvatar\Image;

use KonradMichalik\Typo3LetterAvatar\Enum\ImageDriver;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\{Gd, Gmagick, Imagick};

/**
 * Avatar.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Avatar
{
    public static function create(...$args): LetterAvatarInterface
    {
        $imageDriver = $args['imageDriver'] ?? $GLOBALS['TYPO3_CONF_VARS']['GFX']['processor'];
        unset($args['imageDriver']);

        return match ($imageDriver) {
            ImageDriver::GD => new Gd(...$args),
            ImageDriver::GMAGICK => new Gmagick(...$args),
            default => new Imagick(...$args),
        };
    }
}
