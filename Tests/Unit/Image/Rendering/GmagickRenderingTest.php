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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Image\Rendering;

use KonradMichalik\Typo3LetterAvatar\Enum\Shape;
use KonradMichalik\Typo3LetterAvatar\Image\AbstractImageProvider;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\Gmagick;

/**
 * GmagickRenderingTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class GmagickRenderingTest extends AbstractRenderingTestCase
{
    protected function setUp(): void
    {
        if (!class_exists(\Gmagick::class)) {
            self::markTestSkipped('ext-gmagick is not available.');
        }
    }

    protected function createAvatar(Shape $shape): AbstractImageProvider
    {
        return new Gmagick(
            name: 'John Doe',
            size: self::SIZE,
            fontPath: self::fontPath(),
            foregroundColor: '#FFFFFF',
            backgroundColor: '#000000',
            shape: $shape,
        );
    }

    /**
     * Gmagick exposes no per-pixel accessor, so we round-trip via PNG blob → GD.
     *
     * @return list<array{int, int}>
     */
    protected function extractForegroundPixels(mixed $canvas, int $size): array
    {
        self::assertInstanceOf(\Gmagick::class, $canvas);

        $canvas->setimageformat('png');
        $gd = imagecreatefromstring($canvas->getimageblob());
        self::assertNotFalse($gd, 'Failed to decode Gmagick PNG blob via GD.');

        $pixels = [];
        for ($y = 0; $y < $size; ++$y) {
            for ($x = 0; $x < $size; ++$x) {
                $rgb = imagecolorat($gd, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                if ($r + $g + $b > 600) {
                    $pixels[] = [$x, $y];
                }
            }
        }

        return $pixels;
    }
}
