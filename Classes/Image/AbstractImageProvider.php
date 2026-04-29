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

namespace KonradMichalik\Typo3LetterAvatar\Image;

use KonradMichalik\Typo3LetterAvatar\Configuration;
use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat;
use KonradMichalik\Typo3LetterAvatar\Enum\Shape;
use KonradMichalik\Typo3LetterAvatar\Enum\Transform;
use KonradMichalik\Typo3LetterAvatar\Service\Colorize;
use KonradMichalik\Typo3LetterAvatar\Utility\PathUtility;

/**
 * AbstractImageProvider.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
abstract class AbstractImageProvider
{
    protected ?Colorize $colorizeService = null;

    public function __construct(
        public string $name = '',
        public string $initials = '',
        public int $size = 100,
        public float|int $fontSize = 0.5,
        public string $fontPath = 'EXT:' . Configuration::EXT_KEY . '/Resources/Public/Fonts/arial-bold.ttf',
        public string $foregroundColor = '',
        public string $backgroundColor = '',
        public ColorMode $mode = ColorMode::CUSTOM,
        public string $theme = '',
        public ImageFormat $imageFormat = ImageFormat::PNG,
        public Transform $transform = Transform::NONE,
        public Shape $shape = Shape::CIRCLE,
    ) {
        $this->colorizeService = new Colorize($this);
    }

    public function getImagePath(?string $filename = null): string
    {
        return PathUtility::getImageFolder() . (
            $filename !== null && $filename !== ''
                ? $filename
                : ($this->configToHash() . '.' . $this->imageFormat->value)
        );
    }

    public function getWebPath(?string $filename = null): string
    {
        return PathUtility::getWebPath($filename ?? $this->configToHash() . '.' . $this->imageFormat->value);
    }

    protected function configToHash(): string
    {
        $parts = [
            $this->name,
            $this->initials,
            $this->size,
            $this->fontSize,
            $this->fontPath,
            $this->foregroundColor,
            $this->backgroundColor,
            $this->mode->value,
            $this->theme,
            $this->transform->value,
            $this->shape->value,
        ];
        return hash('sha256', implode('_', $parts));
    }
}
