<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Vnecoms\VendorsCustomRegister\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * CreateCustomerObserver
 */
class SubAccountSaveBefore implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Vnecoms\VendorsCustomRegister\Helper\Process
     */
    protected $helperProcess;

    /**
     * SubAccountSaveBefore constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Vnecoms\VendorsCustomRegister\Helper\Process $helperProcess
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Vnecoms\VendorsCustomRegister\Helper\Process $helperProcess
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helperProcess =$helperProcess;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();
        $this->helperProcess->processBeforeRequest($request);
    }
}
