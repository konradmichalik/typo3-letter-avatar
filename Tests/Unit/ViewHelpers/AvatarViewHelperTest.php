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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\ViewHelpers;

use InvalidArgumentException;
use KonradMichalik\Typo3LetterAvatar\ViewHelpers\AvatarViewHelper;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * AvatarViewHelperTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class AvatarViewHelperTest extends TestCase
{
    private AvatarViewHelper $viewHelper;

    protected function setUp(): void
    {
        $this->viewHelper = new AvatarViewHelper();
        $this->viewHelper->initializeArguments();
    }

    #[Test]
    public function viewHelperExtendsAbstractViewHelper(): void
    {
        self::assertInstanceOf(AbstractViewHelper::class, $this->viewHelper);
    }

    #[Test]
    public function initializeArgumentsRegistersAllExpectedArguments(): void
    {
        $reflection = new ReflectionClass($this->viewHelper);
        $property = $reflection->getProperty('argumentDefinitions');
        $arguments = $property->getValue($this->viewHelper);

        $expectedArgumentNames = [
            'name',
            'initials',
            'size',
            'fontSize',
            'fontPath',
            'foregroundColor',
            'backgroundColor',
            'mode',
            'theme',
            'imageFormat',
            'transform',
            'shape',
        ];

        foreach ($expectedArgumentNames as $name) {
            self::assertArrayHasKey($name, $arguments, "Argument '$name' should be registered");
        }
    }

    #[Test]
    public function noArgumentIsRequired(): void
    {
        $reflection = new ReflectionClass($this->viewHelper);
        $property = $reflection->getProperty('argumentDefinitions');
        $arguments = $property->getValue($this->viewHelper);

        foreach ($arguments as $argumentName => $argumentDefinition) {
            self::assertFalse(
                $argumentDefinition->isRequired(),
                "Argument '$argumentName' should be optional",
            );
        }
    }

    #[Test]
    public function renderThrowsExceptionWhenNeitherNameNorInitialsProvided(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1204028706);

        // Inject empty arguments via reflection
        $arguments = new ReflectionProperty(AbstractViewHelper::class, 'arguments');
        $arguments->setValue($this->viewHelper, ['name' => '', 'initials' => '']);

        $this->viewHelper->render();
    }

    #[Test]
    public function renderThrowsExceptionWithEmptyArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $arguments = new ReflectionProperty(AbstractViewHelper::class, 'arguments');
        $arguments->setValue($this->viewHelper, []);

        $this->viewHelper->render();
    }

    #[Test]
    public function nameArgumentIsOfTypeString(): void
    {
        $reflection = new ReflectionClass($this->viewHelper);
        $property = $reflection->getProperty('argumentDefinitions');
        $arguments = $property->getValue($this->viewHelper);

        self::assertSame('string', $arguments['name']->getType());
        self::assertSame('string', $arguments['initials']->getType());
    }

    #[Test]
    public function sizeArgumentIsOfTypeInteger(): void
    {
        $reflection = new ReflectionClass($this->viewHelper);
        $property = $reflection->getProperty('argumentDefinitions');
        $arguments = $property->getValue($this->viewHelper);

        self::assertSame('integer', $arguments['size']->getType());
    }

    #[Test]
    public function fontSizeArgumentIsOfTypeFloat(): void
    {
        $reflection = new ReflectionClass($this->viewHelper);
        $property = $reflection->getProperty('argumentDefinitions');
        $arguments = $property->getValue($this->viewHelper);

        self::assertSame('float', $arguments['fontSize']->getType());
    }
}
