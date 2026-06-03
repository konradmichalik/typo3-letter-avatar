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

use GdImage;
use KonradMichalik\Typo3LetterAvatar\Enum\Shape;
use KonradMichalik\Typo3LetterAvatar\Image\AbstractImageProvider;
use KonradMichalik\Typo3LetterAvatar\Image\Driver\Gd;

use function extension_loaded;

/**
 * GdRenderingTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class GdRenderingTest extends AbstractRenderingTestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('gd')) {
            self::markTestSkipped('ext-gd is not available.');
        }
    }

    protected function createAvatar(Shape $shape): AbstractImageProvider
    {
        return new Gd(
            name: 'John Doe',
            size: self::SIZE,
            fontPath: self::fontPath(),
            foregroundColor: '#FFFFFF',
            backgroundColor: '#000000',
            shape: $shape,
        );
    }

    /**
     * @return list<array{int, int}>
     */
    protected function extractForegroundPixels(mixed $canvas, int $size): array
    {
        self::assertInstanceOf(GdImage::class, $canvas);

        $pixels = [];
        for ($y = 0; $y < $size; ++$y) {
            for ($x = 0; $x < $size; ++$x) {
                $rgb = imagecolorat($canvas, $x, $y);
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
