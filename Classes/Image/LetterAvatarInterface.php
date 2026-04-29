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

use KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat;

/**
 * LetterAvatarInterface.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
interface LetterAvatarInterface
{
    public function generate(): mixed;

    public function save(?string $path = null, ImageFormat $format = ImageFormat::PNG, int $quality = 90): string;

    public function getImagePath(?string $filename = null): string;

    public function getWebPath(?string $filename = null): string;
}
