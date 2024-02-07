<?php

namespace Magento\OrderIngestion\Plugin;

use Magento\DataExporter\Export\Request\Node;
use Magento\DataExporter\Uuid\ResourceModel\UuidResource;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Service\ExternalOrderService;
use Magento\SalesOrdersDataExporter\Model\Provider\ExternalOrderId;

class AssignSalesChannelToExternalId
{
    private const ORDER_TYPE = "order";
    private const COMMERCE_ORDER_ID_TAG = 'commerceOrderId';
    private const EXTERNAL_ID_TAG = 'externalId';

    /**
     * @var ExternalOrderService
     */
    private ExternalOrderService $externalOrderService;

    /**
     * @var UuidResource
     */
    private UuidResource $salesOrderResource;

    /**
     * @var OrderIngestionLoggerInterface
     */
    private OrderIngestionLoggerInterface $logger;

    /**
     * @param ExternalOrderService $externalOrderService
     * @param UuidResource $salesOrderResource
     * @param OrderIngestionLoggerInterface $logger
     */
    public function __construct(ExternalOrderService $externalOrderService,
                                UuidResource $salesOrderResource,
                                OrderIngestionLoggerInterface $logger)
    {
        $this->externalOrderService = $externalOrderService;
        $this->salesOrderResource = $salesOrderResource;
        $this->logger = $logger;
    }

    /**
     * @param ExternalOrderId $subject
     * @param array $result
     * @param array $values
     * @param Node $node
     * @return array
     * @throws \Safe\Exceptions\StringsException
     */
    public function afterGet(ExternalOrderId $subject, array $result, array $values, Node $node): array
    {
        foreach ($result as $key => $value) {
            if (isset($value[self::COMMERCE_ORDER_ID_TAG], $value[self::EXTERNAL_ID_TAG])) {
                $commerceOrder = $this->salesOrderResource->getAssignedIds([$value[self::COMMERCE_ORDER_ID_TAG]], self::ORDER_TYPE);
                if (isset($commerceOrder[$value[self::COMMERCE_ORDER_ID_TAG]])) {
                    $order = $this->externalOrderService->getExternalOrderById($commerceOrder[$value[self::COMMERCE_ORDER_ID_TAG]]);
                    if ($order !== null) {
                        $result[$key][self::EXTERNAL_ID_TAG]['id'] = $order->getExternalOrderId();
                        $result[$key][self::EXTERNAL_ID_TAG]['salesChannel'] = strtolower($order->getSalesChannel());
                        $this->logger->info(
                            \Safe\sprintf("ExternalId updated: order id %s - external order id: %s",
                                $value[self::COMMERCE_ORDER_ID_TAG],
                                $order->getOrderId()
                            ));
                    }
                }
            }
        }

        return $result;
    }
}
