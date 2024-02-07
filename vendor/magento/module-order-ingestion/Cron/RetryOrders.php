<?php

namespace Magento\OrderIngestion\Cron;

use Magento\OrderIngestion\Api\Data\ExternalOrderInterface;
use Magento\OrderIngestion\Model\Dto\CreateOrderResult;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Model\Serializer\CreateOrderResultSerializer;
use Magento\OrderIngestion\Service\CreateOrder;
use Magento\OrderIngestion\Service\ExternalOrderService;
use Magento\OrderIngestion\Service\GraphQlService;

class RetryOrders
{
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
        $totalCreatedOrders = 0;
        $totalFailedOrders = 0;
        $totalOrdersToBeRetried = 0;
        $this->logger->info('Retry failed orders');
        $retriedOrders = $this->externalOrderService->getExternalOrders(ExternalOrderInterface::STATUS_RETRY);
        $results = [];
        foreach ($retriedOrders as $retriedOrder) {
            try {
                $result = $this->createOrder->fromExternalOrder($retriedOrder);
                $results[] = $result;

                if ($result->getCode() === CreateOrderResult::SUCCESS) {
                    $this->externalOrderService->markAsImported($retriedOrder, $result->getCommerceOrderId());
                    $totalCreatedOrders++;
                    $this->logger->info(sprintf("Successfully imported external order %s.", $retriedOrder->getOrderId()));
                } else {
                    $this->externalOrderService->decrementNumberOfRetries($retriedOrder);
                    $this->logger->error(
                        \Safe\sprintf("Cannot import external order %s. error: %s", $retriedOrder->getOrderId(), $result->getMessage())
                    );
                }
            } catch (\Exception $e) {
                $this->externalOrderService->decrementNumberOfRetries($retriedOrder);
                $this->logger->error(
                    \Safe\sprintf("Cannot import external order %s. error: %s", $retriedOrder->getOrderId(), $e->getMessage())
                );
                $results[] = $result  ?? new CreateOrderResult(
                        $retriedOrder->getExternalOrderId(),
                        '',
                        CreateOrderResult::FAIL,
                        "Cannot create commerce order from external order data."
                );
            }

            if ($retriedOrder->getNumberOfRetries() == 0) {
                $this->externalOrderService->markAsFail($retriedOrder);
                $totalFailedOrders++;
                $this->logger->warning(sprintf("Failed to import external order %s.", $retriedOrder->getOrderId()));
            } else {
                $totalOrdersToBeRetried++;
            }

            $this->logger->info(\Safe\sprintf(
                    "Tried to retry import %d external orders , %d orders created, %d failed, %d orders will be retried",
                    count($retriedOrders), $totalCreatedOrders, $totalFailedOrders, $totalOrdersToBeRetried)
            );

            if (count($results) > 0) {
                $this->logger->info("Sending the number of successfully created orders to the sales channels service.");
                $serializedResults = $this->serializer->serialize($results);
                $this->graphQlService->notifyOrdersCreated($serializedResults);
            }
        }
    }
}
