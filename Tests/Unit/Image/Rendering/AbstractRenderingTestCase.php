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
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Core\{ApplicationContext, Environment};

use function count;
use function dirname;
use function sprintf;

/**
 * AbstractRenderingTestCase.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
abstract class AbstractRenderingTestCase extends TestCase
{
    protected const SIZE = 100;
    protected const TOLERANCE = 0.15;
    protected const MIN_PIXELS = 50;

    private static bool $environmentInitialized = false;

    public static function setUpBeforeClass(): void
    {
        if (self::$environmentInitialized) {
            return;
        }
        $extensionRoot = dirname(__DIR__, 4);
        Environment::initialize(
            new ApplicationContext('Testing'),
            true,
            false,
            $extensionRoot,
            $extensionRoot,
            $extensionRoot.'/.Build/var',
            $extensionRoot.'/.Build/var/config',
            $extensionRoot.'/.Build/public/index.php',
            'UNIX',
        );
        self::$environmentInitialized = true;
    }

    /**
     * @return array<string, array{Shape}>
     */
    public static function shapeProvider(): array
    {
        return [
            'circle' => [Shape::CIRCLE],
            'square' => [Shape::SQUARE],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('shapeProvider')]
    public function testLetterIsCentered(Shape $shape): void
    {
        $avatar = $this->createAvatar($shape);
        $canvas = $avatar->generate();
        $pixels = $this->extractForegroundPixels($canvas, self::SIZE);

        $this->assertLetterIsPresent($pixels);
        $this->assertLetterIsHorizontallyCentered($pixels, self::SIZE);
        $this->assertLetterIsVerticallyCentered($pixels, self::SIZE);
    }

    protected static function fontPath(): string
    {
        return dirname(__DIR__, 4).'/Resources/Public/Fonts/NotoSans-Bold.ttf';
    }

    abstract protected function createAvatar(Shape $shape): AbstractImageProvider;

    /**
     * @return list<array{int, int}>
     */
    abstract protected function extractForegroundPixels(mixed $canvas, int $size): array;

    /**
     * @param list<array{int, int}> $pixels
     */
    protected function assertLetterIsPresent(array $pixels): void
    {
        self::assertGreaterThanOrEqual(
            self::MIN_PIXELS,
            count($pixels),
            sprintf(
                'Expected at least %d foreground pixels, got %d. The letter does not appear to have been drawn.',
                self::MIN_PIXELS,
                count($pixels),
            ),
        );
    }

    /**
     * @param list<array{int, int}> $pixels
     */
    protected function assertLetterIsHorizontallyCentered(array $pixels, int $size): void
    {
        $xs = array_column($pixels, 0);
        $centerX = (min($xs) + max($xs)) / 2;
        $expected = $size / 2;
        $toleranceAbs = $size * self::TOLERANCE;

        self::assertLessThanOrEqual(
            $toleranceAbs,
            abs($centerX - $expected),
            sprintf(
                'Letter is not horizontally centered: bounding-box center X = %.1f, expected %.1f ± %.1f.',
                $centerX,
                $expected,
                $toleranceAbs,
            ),
        );
    }

    /**
     * @param list<array{int, int}> $pixels
     */
    protected function assertLetterIsVerticallyCentered(array $pixels, int $size): void
    {
        $ys = array_column($pixels, 1);
        $centerY = (min($ys) + max($ys)) / 2;
        $expected = $size / 2;
        $toleranceAbs = $size * self::TOLERANCE;

        self::assertLessThanOrEqual(
            $toleranceAbs,
            abs($centerY - $expected),
            sprintf(
                'Letter is not vertically centered: bounding-box center Y = %.1f, expected %.1f ± %.1f.',
                $centerY,
                $expected,
                $toleranceAbs,
            ),
        );
    }
}
