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

namespace KonradMichalik\Typo3LetterAvatar\Tests\Unit\Command;

use KonradMichalik\Typo3LetterAvatar\Command\ClearAvatarsCommand;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;

/**
 * ClearAvatarsCommandTest.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class ClearAvatarsCommandTest extends TestCase
{
    private ClearAvatarsCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
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
        $reflection = new \ReflectionClass(ClearAvatarsCommand::class);

        self::assertTrue($reflection->isFinal());
    }

    #[Test]
    public function commandHasCorrectMethodSignatures(): void
    {
        $reflection = new \ReflectionClass(ClearAvatarsCommand::class);

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
