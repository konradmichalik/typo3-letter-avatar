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

namespace KonradMichalik\Typo3LetterAvatar\Image\Driver;

use GdImage;
use KonradMichalik\Typo3LetterAvatar\Enum\{ImageFormat, Shape};
use KonradMichalik\Typo3LetterAvatar\Image\{AbstractImageProvider, LetterAvatarInterface};
use KonradMichalik\Typo3LetterAvatar\Utility\{PathUtility, StringUtility};
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Gd.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Gd extends AbstractImageProvider implements LetterAvatarInterface
{
    public function generate(): GdImage|false
    {
        $initials = StringUtility::resolveInitials($this->name, $this->initials, $this->transform);
        $canvas = $this->createCanvas();

        if (false === $canvas) {
            return false;
        }

        $bgColor = $this->allocateColor($canvas, $this->colorizeService->resolveBackgroundColor());
        $fgColor = $this->allocateColor($canvas, $this->colorizeService->resolveForegroundColor());

        if (Shape::CIRCLE === $this->shape) {
            imagefilledellipse($canvas, $this->size / 2, $this->size / 2, $this->size, $this->size, $bgColor);
        } elseif (Shape::SQUARE === $this->shape) {
            imagefilledrectangle($canvas, 0, 0, $this->size, $this->size, $bgColor);
        }

        $this->drawText($canvas, $initials, $fgColor);

        return $canvas;
    }

    public function save(?string $path = null, ?ImageFormat $format = null, int $quality = 90): string
    {
        $format ??= $this->imageFormat;

        if (null === $path) {
            $filename = $this->configToHash().'.'.$format->value;
            $path = PathUtility::getImageFolder().$filename;
        }

        $image = $this->generate();
        match ($format) {
            ImageFormat::JPEG => imagejpeg($image, $path, $quality),
            ImageFormat::PNG => imagepng($image, $path),
        };

        imagedestroy($image);

        return $path;
    }

    private function createCanvas(): GdImage|false
    {
        $canvas = imagecreatetruecolor($this->size, $this->size);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        return $canvas;
    }

    private function allocateColor(GdImage $canvas, string $hexColor): int|bool
    {
        [$r, $g, $b] = sscanf($hexColor, '#%02x%02x%02x');

        return imagecolorallocate($canvas, $r, $g, $b);
    }

    private function drawText(GdImage $canvas, string $text, int $color): void
    {
        $fontPath = GeneralUtility::getFileAbsFileName($this->fontPath);
        $fontSize = $this->size * $this->fontSize;
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $text);
        $x = ($this->size - ($textBox[2] - $textBox[0])) / 2;
        $y = ($this->size - $textBox[5] - $textBox[1]) / 2;
        imagettftext($canvas, $fontSize, 0, (int) $x, (int) $y, $color, $fontPath, $text);
    }
}
