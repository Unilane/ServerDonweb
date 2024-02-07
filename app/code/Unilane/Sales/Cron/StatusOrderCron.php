<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Unilane\Sales\Cron;

use Unilane\Sales\Helper\statusOrderHelper as statusOrderHelper;
/**
 * Class SynchronizeWebsiteAttributes
 * @package Magento\Catalog\Cron
 */
class StatusOrderCron
{
    /**
     * @var helper
     */
    private $helper;

    /**
     * SynchronizeWebsiteAttributes constructor.
     * @param ImportC $helper
     */
    public function __construct(statusOrderHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Synchronizes website attribute values if needed
     * @return void
     */
    public function execute()
    {
        $this->helper->statusOrder();
    } 
}
