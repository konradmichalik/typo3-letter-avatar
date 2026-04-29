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

namespace KonradMichalik\Typo3LetterAvatar\Command;

use KonradMichalik\Typo3LetterAvatar\Utility\PathUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ClearAvatarsCommand.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0
 */
final class ClearAvatarsCommand extends Command
{
    protected function configure(): void
    {
        $this->setHelp('Clear all generated letter avatars.')
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'Simulate the deletion process without actually deleting files.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = PathUtility::getImageFolder();
        $output->writeln("🧽 - Clearing all generated letter avatars in <question>$path</question>");

        $imageCount = 0;
        if (is_dir($path)) {
            $files = scandir($path);
            $imageCount = count(array_filter($files, function (string $file) use ($path): bool {
                return is_file($path . DIRECTORY_SEPARATOR . $file) && preg_match('/\.(png|jpg|jpeg)$/i', $file) === 1;
            }));
        }

        if ((bool)$input->getOption('dry-run')) {
            $output->writeln("ℹ️ - <comment>$imageCount</comment> letter avatars would be cleared (dry-run).");
            return Command::SUCCESS;
        }

        $return = GeneralUtility::rmdir($path, true);

        if ($return === false) {
            $output->writeln('❌ - Failed to clear generated letter avatars.');
            return Command::FAILURE;
        }

        $output->writeln("✅  - <comment>$imageCount</comment> letter avatars have been cleared.");

        return Command::SUCCESS;
    }
}
