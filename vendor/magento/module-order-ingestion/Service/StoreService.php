<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Service;

use Magento\OrderIngestion\Model\Config;

class StoreService
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function getAllStoreCodes() {
        return array_map(
            function ($store) {return $store['code'];},
            $this->config->getStoreViews());
    }
}
