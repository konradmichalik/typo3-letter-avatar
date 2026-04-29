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

namespace KonradMichalik\Typo3LetterAvatar\Image\Driver;

use KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat;
use KonradMichalik\Typo3LetterAvatar\Enum\Shape;
use KonradMichalik\Typo3LetterAvatar\Image\AbstractImageProvider;
use KonradMichalik\Typo3LetterAvatar\Image\LetterAvatarInterface;
use KonradMichalik\Typo3LetterAvatar\Utility\PathUtility;
use KonradMichalik\Typo3LetterAvatar\Utility\StringUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Imagick.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
class Imagick extends AbstractImageProvider implements LetterAvatarInterface
{
    public function generate(): \Imagick
    {
        $canvas = $this->createCanvas();
        $backgroundColor = $this->colorizeService->resolveBackgroundColor();

        if ($this->shape === Shape::CIRCLE) {
            $this->drawCircle($canvas, $backgroundColor);
        } elseif ($this->shape === Shape::SQUARE) {
            $this->drawSquare($canvas, $backgroundColor);
        }

        $this->drawText(
            $canvas,
            StringUtility::resolveInitials($this->name, $this->initials, $this->transform),
            $this->colorizeService->resolveForegroundColor()
        );
        return $canvas;
    }

    public function save(?string $path = null, ImageFormat $format = ImageFormat::PNG, int $quality = 90): string
    {
        if (is_null($path)) {
            $filename = $this->configToHash() . '.' . $format->value;
            $path = PathUtility::getImageFolder() . $filename;
        }

        $image = $this->generate();
        $image->setImageFormat($format->value);

        if ($format === ImageFormat::JPEG) {
            $image->setImageCompressionQuality($quality);
        }

        $image->writeImage($path);
        return $path;
    }

    private function createCanvas(): \Imagick
    {
        $canvas = new \Imagick();
        $canvas->newImage($this->size, $this->size, new \ImagickPixel('transparent'));
        $canvas->setImageFormat($this->imageFormat->value);
        return $canvas;
    }

    private function drawCircle(\Imagick $canvas, string $color): void
    {
        $circle = new \ImagickDraw();
        $circle->setFillColor(new \ImagickPixel($color));
        $circle->circle($this->size / 2, $this->size / 2, $this->size / 2, 0);
        $canvas->drawImage($circle);
    }

    private function drawSquare(\Imagick $canvas, string $color): void
    {
        $square = new \ImagickDraw();
        $square->setFillColor(new \ImagickPixel($color));
        $square->rectangle(0, 0, $this->size, $this->size);
        $canvas->drawImage($square);
    }

    private function drawText(\Imagick $canvas, string $text, string $color): void
    {
        $draw = new \ImagickDraw();
        $draw->setFont(GeneralUtility::getFileAbsFileName($this->fontPath));
        $draw->setFontSize($this->size * $this->fontSize);
        $draw->setFillColor(new \ImagickPixel($color));
        $draw->setTextAlignment(\Imagick::ALIGN_CENTER);
        $draw->annotation($this->size / 2, $this->size / 1.5, $text);
        $canvas->drawImage($draw);
    }
}
