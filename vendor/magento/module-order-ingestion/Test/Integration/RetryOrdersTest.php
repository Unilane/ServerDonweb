<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Test\Integration\Job;

use Magento\DataExporter\Uuid\ResourceModel\UuidResource;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\Http;
use Magento\Framework\ObjectManagerInterface;
use Magento\OrderIngestion\Api\Data\ExternalOrderInterface;
use Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface;
use Magento\OrderIngestion\Cron\CreateOrders;
use Magento\OrderIngestion\Cron\RetryOrders;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Model\Serializer\CreateOrderResultSerializer;
use Magento\OrderIngestion\Model\ServiceClientInterface;
use Magento\OrderIngestion\Service\CreateOrder;
use Magento\OrderIngestion\Service\ExternalOrderService;
use Magento\OrderIngestion\Service\GraphQlService;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Magento\OrderIngestion\Model\Carrier;


class RetryOrdersTest extends TestCase
{
    const EXTERNAL_ORDER_1 = 'external-order-1';
    const ORDER_ID = 'ORDER_ID';
    const ORDER_TYPE = "order";
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var RetryOrders
     */
    private $retryOrders;

    /**
     * @var ExternalOrderRepositoryInterface
     */
    private $externalOrderIngestionRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ServiceClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $serviceClientInterface;

    /**
     * @var UuidResource
     */
    private $salesOrderResource;

    /**
     * @var ExternalOrderService
     */
    private $externalOrderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();

        $this->externalOrderIngestionRepository = $this->objectManager->get(ExternalOrderRepositoryInterface::class);
        $this->orderRepository = $this->objectManager->get(OrderRepositoryInterface::class);
        $this->searchCriteriaBuilder = $this->objectManager->get(SearchCriteriaBuilder::class);
        $this->salesOrderResource = $this->objectManager->get(UuidResource::class);

        $this->serviceClientInterface = $this->getMockBuilder(ServiceClientInterface\Proxy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->externalOrderService = $this->objectManager->get(ExternalOrderService::class);
        $this->retryOrders =  new RetryOrders(
            $this->externalOrderService,
            $this->objectManager->get(CreateOrder::class),
            $this->objectManager->get(OrderIngestionLoggerInterface::class),
            new GraphQlService($this->serviceClientInterface, $this->objectManager->get(OrderIngestionLoggerInterface::class)),
            $this->objectManager->get(CreateOrderResultSerializer::class),
        );
    }

    /**
     * @magentoDataFixture Magento_OrderIngestion::Test/Integration/_files/importedretryorders.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testRetryCreationOfFailedOrders(): void
    {
        $this->serviceClientInterface->expects($this->exactly(2))
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/sales-channels/graphql',
                Http::METHOD_POST,
                $this->anything()
            )
            ->willReturn(
                [
                    'data' => [
                        'notifyOrdersIngested' => [
                            'submissionStatus' => [
                                'status' => 'KO',
                                'message' => 'Cannot notify orders'
                            ]
                        ]
                    ],
                    'status' => 200,
                ]
            );

        $this->retryOrders->execute();
        $externalOrder = $this->externalOrderService->getExternalOrderById(self::ORDER_ID);
        $this->assertEquals(1, $externalOrder->getNumberOfRetries());
        $this->assertEquals(ExternalOrderInterface::STATUS_RETRY, $externalOrder->getStatus());

        $this->retryOrders->execute();
        $externalOrder = $this->externalOrderService->getExternalOrderById(self::ORDER_ID);
        $this->assertEquals(0, $externalOrder->getNumberOfRetries());
        $this->assertEquals(ExternalOrderInterface::STATUS_FAIL, $externalOrder->getStatus());
    }
}
