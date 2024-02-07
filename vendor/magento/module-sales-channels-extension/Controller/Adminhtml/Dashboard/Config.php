<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesChannels\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\SalesChannels\Controller\Adminhtml\AbstractProxyController;
use Magento\SalesChannels\Model\Config as ConfigModel;

/**
 * Controller for /config requests
 */
class Config extends AbstractProxyController
{
    const ADMIN_RESOURCE = 'Magento_SalesChannels::saleschannelsdashboard';

    /**
     * A list of function/accessor names from Magento/SalesChannels/Model/Config/.
     * To prevent unintentional function calls we limit this to just what we need from the file for now.
     */
    const ALLOWLIST_PROPERTIES = [
        "getCommerceServicesConnectorPath",
        "getEnvironmentType",
        "getNewAttributePath",
        "getProductsGridPath",
        "getStoreViews",
        "isMagentoServicesConfigured",
        "getCarriers",
        "getInstanceInformation"
    ];

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ConfigModel
     */
    private $config;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param ConfigModel $config
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ConfigModel $config
    ) {
        $this->config = $config;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * For each property requested, access it on the extension's config model (if allowed).
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $jsonResult = $this->resultJsonFactory->create();

        // comma delimited accessor names ie getStoreViews,isMagentoServicesConfigured
        $requestedProperties = $this->getRequest()->getParam('properties');
        $result = [];

        if ($requestedProperties) {
            foreach (explode(",", $requestedProperties) as $property) {
                if (in_array($property, self::ALLOWLIST_PROPERTIES, true)) {
                    $result[$property] = $this->config->$property();
                }
            }
        }

        return $jsonResult->setData($result);
    }
}
