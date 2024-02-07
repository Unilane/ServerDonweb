<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Unilane\SyncProducts\Api;

/**
 * @api
 */
interface SyncProductInterface
{    
    /**
     * @return bool
     */
    public function importProduct();    

    /**
     * @return bool
     */
    public function updateStock();
}
