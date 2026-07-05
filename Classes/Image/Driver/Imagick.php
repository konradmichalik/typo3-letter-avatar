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

use ImagickDraw;
use ImagickPixel;
use KonradMichalik\Typo3LetterAvatar\Enum\{ImageFormat, Shape};
use KonradMichalik\Typo3LetterAvatar\Image\{AbstractImageProvider, LetterAvatarInterface};
use KonradMichalik\Typo3LetterAvatar\Utility\{PathUtility, StringUtility};
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Imagick.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class Imagick extends AbstractImageProvider implements LetterAvatarInterface
{
    public function generate(): \Imagick
    {
        $canvas = $this->createCanvas();
        $backgroundColor = $this->colorizeService->resolveBackgroundColor();

        if (Shape::CIRCLE === $this->shape) {
            $this->drawCircle($canvas, $backgroundColor);
        } elseif (Shape::SQUARE === $this->shape) {
            $this->drawSquare($canvas, $backgroundColor);
        }

        $this->drawText(
            $canvas,
            StringUtility::resolveInitials($this->name, $this->initials, $this->transform),
            $this->colorizeService->resolveForegroundColor(),
        );

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
        $image->setImageFormat($format->value);

        if (ImageFormat::JPEG === $format) {
            $image->setImageCompressionQuality($quality);
        }

        $image->writeImage($path);

        return $path;
    }

    private function createCanvas(): \Imagick
    {
        $canvas = new \Imagick();
        $canvas->newImage($this->size, $this->size, new ImagickPixel('transparent'));
        $canvas->setImageFormat($this->imageFormat->value);

        return $canvas;
    }

    private function drawCircle(\Imagick $canvas, string $color): void
    {
        $circle = new ImagickDraw();
        $circle->setFillColor(new ImagickPixel($color));
        $circle->circle($this->size / 2, $this->size / 2, $this->size / 2, 0);
        $canvas->drawImage($circle);
    }

    private function drawSquare(\Imagick $canvas, string $color): void
    {
        $square = new ImagickDraw();
        $square->setFillColor(new ImagickPixel($color));
        $square->rectangle(0, 0, $this->size, $this->size);
        $canvas->drawImage($square);
    }

    private function drawText(\Imagick $canvas, string $text, string $color): void
    {
        $draw = new ImagickDraw();
        $draw->setFont(GeneralUtility::getFileAbsFileName($this->fontPath));
        $draw->setFontSize($this->size * $this->fontSize);
        $draw->setFillColor(new ImagickPixel($color));
        $draw->setTextAlignment(\Imagick::ALIGN_CENTER);
        $draw->annotation($this->size / 2, $this->size / 1.5, $text);
        $canvas->drawImage($draw);
    }
}
