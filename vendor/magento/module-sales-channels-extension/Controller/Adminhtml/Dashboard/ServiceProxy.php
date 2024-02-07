<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesChannels\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\SalesChannels\Controller\Adminhtml\AbstractProxyController;
use Magento\SalesChannels\Model\ServiceClientInterface;

/**
 * Controller that proxies requests to the requested endpoint
 */
class ServiceProxy extends AbstractProxyController
{
    const ADMIN_RESOURCE = 'Magento_SalesChannels::saleschannelsdashboard';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var ServiceClientInterface
     */
    private $serviceClient;

    /**
     * @param Context $context
     * @param ServiceClientInterface $serviceClient
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        ServiceClientInterface $serviceClient,
        JsonFactory $resultJsonFactory
    ) {
        $this->serviceClient = $serviceClient;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Route serviceproxy call to appropriate handler.
     */
    public function execute()
    {
        // TODO: Refactor to use incoming data from request/UI.
        // https://wiki.corp.adobe.com/pages/viewpage.action?spaceKey=merchantsolutions&title=Generic+Services+Middleware+Extension
        // Much of the routing or request info, such as headers, can be obtained from the request headers:
        //   x-services-path: /sales-channels/graphql
        //   x-services-header-content-type: application/json
        $jsonResult = $this->resultJsonFactory->create();
        $result = $this->serviceClient->request(
            ['Content-Type' => 'application/json'],
            '/sales-channels/graphql',
            Http::METHOD_POST,
            $this->getRequest()->getContent()
        );
        return $jsonResult->setData($result);
    }
}
