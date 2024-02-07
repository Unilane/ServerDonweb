<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\SalesChannels\Model;

class ModuleVersionReader
{
    /** @var string|null */
    private static $version;

    public function getVersion()
    {
        if (null === self::$version) {
            $version = 'UNKNOWN';
            $composerFile = __DIR__ . '/../composer.json';
            if (file_exists($composerFile)) {
                $composerData = json_decode(file_get_contents($composerFile), true);
                if (is_array($composerData) && isset($composerData['version'])) {
                    $version = $composerData['version'];
                }
            }
            self::$version = $version;
        }
        return self::$version;
    }
}
