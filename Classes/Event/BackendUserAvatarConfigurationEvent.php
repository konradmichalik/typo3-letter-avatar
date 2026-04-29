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

namespace KonradMichalik\Typo3LetterAvatar\Event;

/**
 * BackendUserAvatarConfigurationEvent.
 *
 * @author Konrad Michalik <hej@konradmichalik.dev>
 * @license GPL-2.0-or-later
 */
class BackendUserAvatarConfigurationEvent
{
    final public const NAME = 'typo3_letter_avatar.backend_user.modify_avatar_provider';

    public function __construct(
        protected array $backendUser,
        protected array $configuration,
    ) {}

    public function getBackendUser(): array
    {
        return $this->backendUser;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
