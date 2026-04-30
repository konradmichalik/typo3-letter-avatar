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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Command;

use KonradMichalik\Typo3LetterAvatar\Command\ClearAvatarsCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;

/**
 * ClearAvatarsCommandTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
final class ClearAvatarsCommandTest extends TestCase
{
    private ClearAvatarsCommand $command;

    protected function setUp(): void
    {
        $this->command = new ClearAvatarsCommand();
    }

    #[Test]
    public function commandExtendsSymfonyCommand(): void
    {
        self::assertInstanceOf(Command::class, $this->command);
    }

    #[Test]
    public function commandImplementsConfigureMethod(): void
    {
        self::assertTrue(method_exists($this->command, 'configure'));
    }

    #[Test]
    public function commandImplementsExecuteMethod(): void
    {
        self::assertTrue(method_exists($this->command, 'execute'));
    }

    #[Test]
    public function commandIsFinal(): void
    {
        $reflection = new ReflectionClass(ClearAvatarsCommand::class);

        self::assertTrue($reflection->isFinal());
    }

    #[Test]
    public function commandHasCorrectMethodSignatures(): void
    {
        $reflection = new ReflectionClass(ClearAvatarsCommand::class);

        $configureMethod = $reflection->getMethod('configure');
        $executeMethod = $reflection->getMethod('execute');

        self::assertTrue($configureMethod->isProtected());
        self::assertTrue($executeMethod->isProtected());
        self::assertSame('void', $configureMethod->getReturnType()?->getName());
        self::assertSame('int', $executeMethod->getReturnType()?->getName());
    }

    #[Test]
    public function commandCanBeInstantiated(): void
    {
        $command = new ClearAvatarsCommand();

        self::assertInstanceOf(ClearAvatarsCommand::class, $command);
        self::assertInstanceOf(Command::class, $command);
    }
}
