# Usage

> **Note:** Examples below apply to TYPO3 v13.4 LTS and v14.x. For v11/v12 compatibility, use the 1.x line of this extension.

## Backend Users

The extension works automatically for backend users after installation. Run setup command to generate avatars for existing users:

```bash
vendor/bin/typo3 extension:setup --extension=typo3_letter_avatar
```

## Programmatic Usage

Generate avatar images in your code:

```php
use KonradMichalik\Typo3LetterAvatar\Image\Avatar;
use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;

// Generate avatar entity
$avatar = Avatar::create(
    name: 'Konrad Michalik',
    mode: ColorMode::RANDOM
);

// Save avatar image to default path
$avatar->save();

// Get web path of generated image
$webPath = $avatar->getWebPath();
```

> **Note:** See available parameters in [`AbstractImageProvider`](../Classes/Image/AbstractImageProvider.php#L18)

## ViewHelper

Use in Fluid templates for frontend users:

```html
<html xmlns:letter="http://typo3.org/ns/KonradMichalik/Typo3LetterAvatar/ViewHelpers">

<img src="{letter:avatar(name: 'John Doe')}" alt="Avatar of John Doe" />
```

> **Note:** See available arguments in [`AvatarViewHelper`](../Classes/ViewHelpers/AvatarViewHelper.php)

## Console Command

Clear and regenerate all avatars:

```bash
vendor/bin/typo3 avatar:clear
```

## EventListener

Modify avatar configuration per backend user:

```php
<?php

declare(strict_types=1);

namespace Vendor\Package\EventListener;

use KonradMichalik\Typo3LetterAvatar\Enum\ColorMode;
use KonradMichalik\Typo3LetterAvatar\Event\BackendUserAvatarConfigurationEvent;

class ModifyLetterAvatarEventListener
{
    public function __invoke(BackendUserAvatarConfigurationEvent $event): void
    {
        $backendUser = $event->getBackendUser();
        
        // Example: Custom colors for admin users
        if ($backendUser['admin'] === 1) {
            $configuration = $event->getConfiguration();
            $configuration['mode'] = ColorMode::CUSTOM;
            $configuration['foreground'] = '#000000';
            $configuration['background'] = '#FFFFFF';
            
            $event->setConfiguration($configuration);
        }
    }
}
```

> **Note:** Remember to [register the event listener](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/Events/EventDispatcher/Index.html#registering-the-event-listener-via-file-services-yaml).