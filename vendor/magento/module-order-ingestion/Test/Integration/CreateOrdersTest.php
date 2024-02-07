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


class CreateOrdersTest extends TestCase
{
    const EXTERNAL_ORDER_1 = 'external-order-1';
    const ORDER_ID_WITHOUT_TAX = 'ORDER_ID_WITHOUT_TAX';
    const ORDER_ID_WITH_TAX = 'ORDER_ID_WITH_TAX';
    const ORDER_ID_BAD_LASTNAME = 'ORDER_ID_BAD_LASTNAME';
    const ORDER_ID_BAD_STORE = 'ORDER_ID_BAD_STORE';
    const ORDER_TYPE = "order";
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CreateOrders
     */
    private $createOrders;

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
        $this->createOrders =  new CreateOrders(
            $this->externalOrderService,
            $this->objectManager->get(CreateOrder::class),
            $this->objectManager->get(OrderIngestionLoggerInterface::class),
            new GraphQlService($this->serviceClientInterface, $this->objectManager->get(OrderIngestionLoggerInterface::class)),
            $this->objectManager->get(CreateOrderResultSerializer::class),
        );
    }

    /**
     * @magentoDataFixture Magento_OrderIngestion::Test/Integration/_files/importedorders.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testExecuteWithTax(): void
    {
        $this->serviceClientInterface->expects($this->once())
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/sales-channels/graphql',
                Http::METHOD_POST,
                $this->anything()
            )
            ->willReturn(
                ['data' =>
                    ['notifyOrdersIngested' =>
                        ['submissionStatus' =>
                            [
                                'status' => 'OK',
                                'message' => 'Notification OK'
                            ],
                        ]
                    ],
                    'status' => 200,
                ]
            );

        $this->createOrders->execute();

        $externalOrder = $this->externalOrderService->getExternalOrderById(self::ORDER_ID_WITH_TAX);
        $this->assertNotNull($externalOrder);
        $this->assertEquals(ExternalOrderInterface::STATUS_IMPORTED, $externalOrder->getStatus());

        $orders = $this->orderRepository->getList($this->searchCriteriaBuilder
            ->addFilter('entity_id', (int) $externalOrder->getCommerceOrderId())
            ->create())->getItems();
        $this->assertCount(1, $orders);

        /**
         * @var \Magento\Sales\Api\Data\OrderInterface $order
         */
        $order = reset($orders);
        $items = $order->getItems();
        $this->assertCount(2, $items);


        $this->assertEquals(11, $order->getShippingAmount());
        $this->assertEquals(12.1, $order->getShippingInclTax());
        $this->assertEquals(1.1, $order->getShippingTaxAmount());
        $this->assertEquals(1.3, $order->getTaxAmount());
        $this->assertEquals(32.3, $order->getGrandTotal());
        $this->assertEquals(1, $order->getStoreId());
        $this->assertEquals(20, $order->getSubtotal());
        $this->assertEquals(20.2, $order->getSubtotalInclTax());

        $this->assertEquals('demo@examle.com', $order->getCustomerEmail());
        $this->assertEquals("offline_channel_payment", $order->getPayment()->getMethod());
        $this->assertEquals(self::EXTERNAL_ORDER_1, $order->getPayment()->getPoNumber());
        $this->assertEquals(Carrier\Method::CHANNEL_SHIPPING, $order->getShippingMethod());

        /** @var \Magento\Sales\Api\Data\OrderItemInterface $orderItem */
        foreach ($items as $orderItem) {
            $this->assertEquals('simple', $orderItem->getSku());
            $this->assertEquals(1, $orderItem->getQtyOrdered());
            $this->assertEquals(10, $orderItem->getPrice());
            $this->assertEquals(10.1, $orderItem->getPriceInclTax());
            $this->assertEquals(10, $orderItem->getRowTotal());
            $this->assertEquals(10.1, $orderItem->getRowTotalInclTax());
            $this->assertEquals(0.1, $orderItem->getTaxAmount());
        }

        $ingestOrderFail = $this->externalOrderIngestionRepository->getByOrderId(self::ORDER_ID_BAD_LASTNAME);
        $this->assertEquals(ExternalOrderInterface::STATUS_RETRY, $ingestOrderFail->getStatus());

        $uuidData = $this->salesOrderResource->getAssignedIds([$order->getEntityId()], self::ORDER_TYPE);
        $this->assertCount(1, $uuidData);
        $this->assertEquals(self::ORDER_ID_WITH_TAX, $uuidData[$order->getEntityId()]);
    }

    /**
     * @magentoDataFixture Magento_OrderIngestion::Test/Integration/_files/importedordersfail.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testExecuteWhenAllOrdersFail(): void
    {
        $this->serviceClientInterface->expects($this->once())
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/sales-channels/graphql',
                Http::METHOD_POST,
                $this->anything()
            )
            ->willReturn(
                ['data' =>
                    ['notifyOrdersIngested' =>
                        ['submissionStatus' =>
                            [
                                'status' => 'OK',
                                'message' => 'Notification OK'
                            ],
                        ]
                    ],
                    'status' => 200,
                ]
            );

        $this->createOrders->execute();
        $orders = $this->orderRepository->getList($this->searchCriteriaBuilder
            ->create())->getItems();
        $this->assertEmpty($orders);

        $ingestOrderFail = $this->externalOrderIngestionRepository->getByOrderId(self::ORDER_ID_BAD_STORE);
        $this->assertEquals(ExternalOrderInterface::STATUS_RETRY, $ingestOrderFail->getStatus());
    }

    /**
     * @magentoDataFixture Magento_OrderIngestion::Test/Integration/_files/importedorders.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testExecuteWithoutTax(): void
    {
        $this->serviceClientInterface->expects($this->once())
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/sales-channels/graphql',
                Http::METHOD_POST,
                $this->anything()
            )
            ->willReturn(
                ['data' =>
                    ['notifyOrdersIngested' =>
                        ['submissionStatus' =>
                            [
                                'status' => 'OK',
                                'message' => 'Notification OK'
                            ],
                        ]
                    ],
                    'status' => 200,
                ]
            );

        $this->createOrders->execute();
        $externalOrder = $this->externalOrderService->getExternalOrderById(self::ORDER_ID_WITHOUT_TAX);
        $this->assertNotNull($externalOrder);
        $this->assertEquals(ExternalOrderInterface::STATUS_IMPORTED, $externalOrder->getStatus());

        $orders = $this->orderRepository->getList($this->searchCriteriaBuilder
            ->addFilter('entity_id', $externalOrder->getCommerceOrderId())
            ->create())->getItems();
        $this->assertCount(1, $orders);

        /**
         * @var \Magento\Sales\Api\Data\OrderInterface $order
         */
        $order = reset($orders);
        $items = $order->getItems();
        $item = reset($items);

        $this->assertEquals(12.1, $order->getShippingAmount());
        $this->assertEquals(12.1, $order->getShippingInclTax());
        $this->assertEquals(0.0, $order->getShippingTaxAmount());
        $this->assertEquals(0.0, $order->getTaxAmount());
        $this->assertEquals(22.1, $order->getGrandTotal());
        $this->assertEquals(1, $order->getStoreId());
        $this->assertEquals('demo@examle.com', $order->getCustomerEmail());
        $this->assertEquals("offline_channel_payment", $order->getPayment()->getMethod());
        $this->assertEquals("order_without_item_tax", $order->getPayment()->getPoNumber());

        $this->assertCount(1, $items);

        $this->assertEquals('simple', $item->getSku());
        $this->assertEquals(1, $item->getQtyOrdered());
        $this->assertEquals(10, $item->getPrice());
        $this->assertEquals(10, $item->getPriceInclTax());
        $this->assertEquals(10, $item->getRowTotal());
        $this->assertEquals(10, $item->getRowTotalInclTax());
        $this->assertEquals(0, $item->getTaxAmount());

        $ingestOrderFail = $this->externalOrderIngestionRepository->getByOrderId(self::ORDER_ID_BAD_LASTNAME);
        $this->assertEquals(ExternalOrderInterface::STATUS_RETRY, $ingestOrderFail->getStatus());

        $uuidData = $this->salesOrderResource->getAssignedIds([$order->getEntityId()], self::ORDER_TYPE);
        $this->assertCount(1, $uuidData);
        $this->assertEquals(self::ORDER_ID_WITHOUT_TAX, $uuidData[$order->getEntityId()]);
    }

    /**
     * @magentoDataFixture Magento_OrderIngestion::Test/Integration/_files/importedorders.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testExecuteGraphqlFail(): void
    {
        $this->serviceClientInterface->expects($this->once())
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/sales-channels/graphql',
                Http::METHOD_POST,
                $this->anything()
            )
            ->willReturn(
                [
                    'status' => 500,
                    'message' => 'GraphQl Error',
                ]
            );

        $this->createOrders->execute();
    }

    /**
     * @magentoDataFixture Magento_OrderIngestion::Test/Integration/_files/importedorders.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testExecuteGraphqlNotificationError(): void
    {
        $this->serviceClientInterface->expects($this->once())
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

        $this->createOrders->execute();
    }
}
