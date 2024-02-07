<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Cron;

use Magento\OrderIngestion\Exception\ExternalOrderValidationException;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Model\Validation\ExternalOrderValidator;
use Magento\OrderIngestion\Service\GraphQlService;
use Magento\OrderIngestion\Service\ExternalOrderService;
use Magento\OrderIngestion\Service\StoreService;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class ImportExternalOrders
{
    private const SALES_CHANNEL_WALMART = "WALMART";
    private const CREATED_AFTER_PERIOD = '-2 hour';

    /**
     * @var ExternalOrderService
     */
    private ExternalOrderService $externalOrderService;

    /**
     * @var GraphQlService
     */
    private GraphQlService $graphQl;

    /**
     * @var StoreService
     */
    private StoreService $storeService;

    /**
     * @var OrderIngestionLoggerInterface
     */
    private OrderIngestionLoggerInterface $logger;

    /**
     * @var JsonSerializer
     */
    private JsonSerializer $jsonSerializer;

    private ExternalOrderValidator $externalOrderValidator;

    /**
     * @param ExternalOrderService $externalOrderService
     * @param GraphQlService $graphQl
     * @param StoreService $storeService
     * @param OrderIngestionLoggerInterface $logger
     * @param ExternalOrderValidator $externalOrderValidator
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        ExternalOrderService          $externalOrderService,
        GraphQlService                $graphQl,
        StoreService                  $storeService,
        OrderIngestionLoggerInterface $logger,
        ExternalOrderValidator        $externalOrderValidator,
        JsonSerializer                $jsonSerializer
    )
    {
        $this->externalOrderService = $externalOrderService;
        $this->graphQl = $graphQl;
        $this->storeService = $storeService;
        $this->logger = $logger;
        $this->externalOrderValidator = $externalOrderValidator;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @throws \JsonException
     */
    public function execute(): void
    {
        $this->logger->info("Importing orders from the order service");
        $stores = $this->storeService->getAllStoreCodes();
        $salesChannel = self::SALES_CHANNEL_WALMART;
        $createdAfter = (new \DateTime(self::CREATED_AFTER_PERIOD))->format('Y-m-d\TH:i:s\Z');
        $createdBefore = (new \DateTime())->format('Y-m-d\TH:i:s\Z');

        foreach ($stores as $store) {
            $this->import($store, $salesChannel, $createdAfter, $createdBefore);
        }
    }

    /**
     * @param $store
     * @param string $salesChannel
     * @param string $createdAfter
     * @param string $createdBefore
     */
    private function import(string $store, string $salesChannel, string $createdAfter, string $createdBefore): void
    {
        $this->logger->info(sprintf("Getting orders from store %s", $store));

        $salesOrderServiceNewOrders = $this->graphQl->getOrdersServiceOrders($store, $salesChannel, $createdAfter, $createdBefore);

        $fetched = count($salesOrderServiceNewOrders);
        $imported = 0;
        foreach ($salesOrderServiceNewOrders as $salesOrderServiceNewOrder) {
            try {
                $this->externalOrderValidator->validate($salesOrderServiceNewOrder);
                $order = $this->externalOrderService->createExternalOrder(
                    $salesOrderServiceNewOrder['orderId']['id'],
                    $salesOrderServiceNewOrder['externalId']['id'],
                    $salesOrderServiceNewOrder['storeViewCode'],
                    $salesOrderServiceNewOrder['externalId']['salesChannel'],
                    $this->jsonSerializer->serialize($salesOrderServiceNewOrder)
                );
                if ($order && $order->getId()) {
                    $imported++;
                    $this->logger->debug(sprintf("Order with externalId '%s' and store view '%s' imported", $order->getExternalOrderId(), $order->getStoreViewCode()));
                }
            } catch (\InvalidArgumentException|ExternalOrderValidationException $e) {
                $this->logger->error(
                    sprintf(
                        "Cannot import order %s. %s",
                        $salesOrderServiceNewOrder['orderId']['id'] ?? '',
                        $e->getMessage()
                    )
                );
            }
        }

        $this->logger->info(sprintf("%d orders retrieved from the order service: %d orders imported", $fetched, $imported));
    }
}
