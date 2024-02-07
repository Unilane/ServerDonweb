<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Cron;

use Magento\OrderIngestion\Api\Data\ExternalOrderInterface;
use Magento\OrderIngestion\Model\Dto\CreateOrderResult;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Model\Serializer\CreateOrderResultSerializer;
use Magento\OrderIngestion\Service\CreateOrder;
use Magento\OrderIngestion\Service\ExternalOrderService;
use Magento\OrderIngestion\Service\GraphQlService;

class CreateOrders
{
    private const INITIAL_RETRIES = 5;

    /**
     * @var ExternalOrderService
     */
    private ExternalOrderService $externalOrderService;

    /**
     * @var CreateOrder
     */
    private CreateOrder $createOrder;

    /**
     * @var OrderIngestionLoggerInterface
     */
    private OrderIngestionLoggerInterface $logger;

    /**
     * @var GraphQlService
     */
    private GraphQlService $graphQlService;

    /**
     * @var CreateOrderResultSerializer
     */
    private CreateOrderResultSerializer $serializer;

    /**
     * @param ExternalOrderService $externalOrderService
     * @param CreateOrder $createOrder
     * @param OrderIngestionLoggerInterface $logger
     * @param GraphQlService $graphQlService
     * @param CreateOrderResultSerializer $serializer
     */
    public function __construct(
        ExternalOrderService          $externalOrderService,
        CreateOrder                   $createOrder,
        OrderIngestionLoggerInterface $logger,
        GraphQlService                $graphQlService,
        CreateOrderResultSerializer   $serializer
    )
    {
        $this->externalOrderService = $externalOrderService;
        $this->createOrder = $createOrder;
        $this->logger = $logger;
        $this->graphQlService = $graphQlService;
        $this->serializer = $serializer;
    }

    public function execute(): void
    {
        $this->logger->info('Creating orders from external orders');
        $receivedOrders = $this->externalOrderService->getExternalOrders();
        $totalCreatedOrders = 0;
        $totalFailedOrders = 0;

        /**
         * @var CreateOrderResult[] $results
         */
        $results = [];
        foreach ($receivedOrders as $receivedOrder) {
            try {
                $result = $this->createOrder->fromExternalOrder($receivedOrder);
                $results[] = $result;

                if ($result->getCode() === CreateOrderResult::SUCCESS) {
                    $this->externalOrderService->markAsImported($receivedOrder, $result->getCommerceOrderId());
                    $totalCreatedOrders++;
                    $this->logger->info(sprintf("Successfully imported external order %s.", $receivedOrder->getOrderId()));
                } else {
                    $this->externalOrderService->markAsRetry($receivedOrder, self::INITIAL_RETRIES);
                    $totalFailedOrders++;
                }
            } catch (\Exception $e) {
                $results[] = $result  ?? new CreateOrderResult(
                        $receivedOrder->getExternalOrderId(),
                        '',
                        CreateOrderResult::FAIL,
                        "Cannot create commerce order from external order data."
                    );
                $this->externalOrderService->markAsRetry($receivedOrder, self::INITIAL_RETRIES);
                $totalFailedOrders++;
                $this->logger->error(
                    \Safe\sprintf("Cannot import external order %s. error: %s", $receivedOrder->getOrderId(), $e->getMessage())
                );

            }
        }
        $this->logger->info(\Safe\sprintf(
            "Tried to import %d external orders , %d orders created, %d failed orders that will be retried",
            count($receivedOrders), $totalCreatedOrders, $totalFailedOrders)
        );

        if (count($results) > 0) {
            $this->logger->info("Sending the number of successfully created orders to the sales channels service.");
            $serializedResults = $this->serializer->serialize($results);
            $this->graphQlService->notifyOrdersCreated($serializedResults);
        }
    }
}
