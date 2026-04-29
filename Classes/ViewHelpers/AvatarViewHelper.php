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

namespace KonradMichalik\Typo3LetterAvatar\ViewHelpers;

use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Enum\ImageFormat;
use KonradMichalik\Typo3LetterAvatar\Enum\Shape;
use KonradMichalik\Typo3LetterAvatar\Enum\Transform;
use KonradMichalik\Typo3LetterAvatar\Image\Avatar;
use KonradMichalik\Typo3LetterAvatar\Utility\ConfigurationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * AvatarViewHelper.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
class AvatarViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'name',
            'string',
            'Name for the avatar',
            false
        );
        $this->registerArgument(
            'initials',
            'string',
            'Initials for the avatar',
            false
        );
        $this->registerArgument(
            'size',
            'integer',
            'Size of the avatar',
            false
        );
        $this->registerArgument(
            'fontSize',
            'float',
            'Font size of the avatar',
            false
        );
        $this->registerArgument(
            'fontPath',
            'string',
            'Path to the font file',
            false
        );
        $this->registerArgument(
            'foregroundColor',
            'string',
            'Foreground color of the avatar',
            false
        );
        $this->registerArgument(
            'backgroundColor',
            'string',
            'Background color of the avatar',
            false
        );
        $this->registerArgument(
            'mode',
            'string',
            'Color mode of the avatar (e.g. custom)',
            false
        );
        $this->registerArgument(
            'theme',
            'string',
            'Theme of the avatar',
            false
        );
        $this->registerArgument(
            'imageFormat',
            'string',
            'Image format of the avatar (e.g. png or jpeg)',
            false
        );
        $this->registerArgument(
            'transform',
            'string',
            'Text transformation (e.g. uppercase)',
            false
        );
        $this->registerArgument(
            'shape',
            'string',
            'Image shape (e.g. circle or square)',
            false
        );
    }

    public function render(): string
    {
        if (($this->arguments['name'] ?? '') === '' && ($this->arguments['initials'] ?? '') === '') {
            throw new \InvalidArgumentException('Either name or initials must be provided', 1204028706);
        }

        $configuration = [
            'name' => $this->arguments['name'] ?? '',
            'initials' => $this->arguments['initials'] ?? '',
            'mode' => (isset($this->arguments['mode']) && $this->arguments['mode'] !== '') ? ColorMode::tryFrom($this->arguments['mode']) : ConfigurationUtility::get('mode', ColorMode::class),
            'theme' => $this->arguments['theme'] ?? ConfigurationUtility::get('theme'),
            'size' => $this->arguments['size'] ?? ConfigurationUtility::get('size'),
            'fontSize' => $this->arguments['fontSize'] ?? ConfigurationUtility::get('fontSize'),
            'fontPath' => $this->arguments['fontPath'] ?? ConfigurationUtility::get('fontPath'),
            'imageFormat' => (isset($this->arguments['imageFormat']) && $this->arguments['imageFormat'] !== '') ? ImageFormat::tryFrom($this->arguments['imageFormat']) : ConfigurationUtility::get('imageFormat', ImageFormat::class),
            'transform' => (isset($this->arguments['transform']) && $this->arguments['transform'] !== '') ? Transform::tryFrom($this->arguments['transform']) : ConfigurationUtility::get('transform', Transform::class),
            'shape' => (isset($this->arguments['shape']) && $this->arguments['shape'] !== '') ? Shape::tryFrom($this->arguments['shape']) : ConfigurationUtility::get('shape', Shape::class),
        ];

        $avatarService = Avatar::create(...$configuration);

        if (!file_exists($avatarService->getImagePath())) {
            $avatarService->save();
        }

        return $avatarService->getWebPath();
    }
}
