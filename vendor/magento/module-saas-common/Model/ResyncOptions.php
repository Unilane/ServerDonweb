<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSCommon\Model;

/**
 * Class responsible for resync options handling
 */
class ResyncOptions
{
    /**
     * @var bool
     */
    private $isDryRun;

    /**
     * @param bool $isDryRun
     */
    public function __construct(
        bool $isDryRun = false
    ) {
        $this->isDryRun = $isDryRun;
    }

    /**
     * @param bool $dryRun
     * @return void
     */
    public function setIsDryRun(bool $dryRun): void
    {
        $this->isDryRun = $dryRun;
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->isDryRun;
    }
}
