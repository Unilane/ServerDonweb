<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilane\SyncProducts\Cron;

use Unilane\SyncProducts\Helper\ImportC as ImportC;
/**
 * Class SynchronizeWebsiteAttributes
 * @package Magento\Catalog\Cron
 */
class ImportCron
{
    /**
     * @var helper
     */
    private $helper;

    /**
     * SynchronizeWebsiteAttributes constructor.
     * @param ImportC $helper
     */
    public function __construct(ImportC $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Synchronizes website attribute values if needed
     * @return void
     */
    public function execute()
    {
        $this->helper->importProduct();
    }
    /**
     * Synchronizes website attribute values if needed
     * @return void
     */
    public function update()
    {
        $this->helper->updateStock();
    }
}
