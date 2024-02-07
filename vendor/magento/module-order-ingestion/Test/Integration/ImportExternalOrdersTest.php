<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Test\Integration\Job;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Request\Http;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface;
use Magento\OrderIngestion\Cron\ImportExternalOrders;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Model\ServiceClientInterface;
use Magento\OrderIngestion\Model\Validation\ExternalOrderValidator;
use Magento\OrderIngestion\Service\GraphQlService;
use Magento\OrderIngestion\Service\ExternalOrderService;
use Magento\OrderIngestion\Service\StoreService;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ImportExternalOrdersTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ImportExternalOrders
     */
    private $importExternalOrders;

    /**
     * @var ServiceClientInterface\Proxy|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $serviceClientInterface;

    /**
     * @var ExternalOrderRepositoryInterface
     */
    private $externalOrderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = Bootstrap::getObjectManager();

        $this->externalOrderRepository = $this->objectManager->get(ExternalOrderRepositoryInterface::class);
        $this->searchCriteriaBuilder = $this->objectManager->get(SearchCriteriaBuilder::class);

        $this->serviceClientInterface = $this->getMockBuilder(ServiceClientInterface\Proxy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->importExternalOrders = new ImportExternalOrders(
            $this->objectManager->get(ExternalOrderService::class),
            new GraphQlService($this->serviceClientInterface, $this->objectManager->get(OrderIngestionLoggerInterface::class)),
            $this->objectManager->get(StoreService::class),
            $this->objectManager->get(OrderIngestionLoggerInterface::class),
            $this->objectManager->get(ExternalOrderValidator::class),
            $this->objectManager->get(Json::class)
        );
    }

    public function testExecute()
    {
        $this->serviceClientInterface->expects($this->once())
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/salesorders/graphql',
                Http::METHOD_POST,
                $this->anything()
            )
            ->willReturn(
                ['data' =>
                    ['getOrdersByDate' =>
                        ['orders' =>
                            [
                                $this->buildOrder('1234'),
                                $this->buildOrder('1234'),
                                $this->buildOrder('5678'),
                                $this->buildOrder('5678'),
                            ],
                        ]
                    ],
                'status' => 200,
                ]
            );

        $this->importExternalOrders->execute();

        $orders = $this->externalOrderRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        $this->assertCount(2, $orders);

        $this->assertTrue($this->hasOrderId($orders, '1234'), 'Order 1234 should be created');
        $this->assertTrue($this->hasOrderId($orders, '5678'), 'Order 5678 should be created');
    }

    public function testExecuteWhenThereAreMissingFields()
    {
        $this->serviceClientInterface->expects($this->once())
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/salesorders/graphql',
                Http::METHOD_POST,
                $this->anything()
            )
            ->willReturn(
                ['data' =>
                    ['getOrdersByDate' =>
                        ['orders' =>
                            [
                                ['orderId' =>
                                    [
                                        'id' => 1234
                                    ]
                                ],
                            ],
                        ]
                    ],
                    'status' => 200,
                ]
            );

        $this->importExternalOrders->execute();

        $orders = $this->externalOrderRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        $this->assertCount(0, $orders);
    }

    private function buildOrder(string $orderId) : array {
        return [
            'orderId' => [
                'id' => $orderId
            ],
            'externalId' => [
                'id' => "external-id-$orderId",
                'salesChannel' => 'WALMART'
            ],
            'storeViewCode' => 'default',
        ];
    }

    private function hasOrderId(array $orders, string $orderId): bool
    {
        foreach ($orders as $order) {
            if ($order->getOrderId() === $orderId) {
                return true;
            }
        }
        return false;
    }

    public function testExecuteGraphqlFail(): void
    {
        $this->serviceClientInterface->expects($this->once())
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/salesorders/graphql',
                Http::METHOD_POST,
                $this->anything()
            )
            ->willReturn(
                [
                    'status' => 500,
                    'message' => 'GraphQl Error',
                ]
            );

        $this->importExternalOrders->execute();
    }

    public function testExecuteGraphqlNotificationError(): void
    {
        $this->serviceClientInterface->expects($this->once())
            ->method('request')
            ->with(
                ['Content-Type' => 'application/json'],
                '/salesorders/graphql',
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

        $this->importExternalOrders->execute();
    }
}
