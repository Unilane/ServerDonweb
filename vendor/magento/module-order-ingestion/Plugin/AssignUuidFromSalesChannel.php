<?php

namespace Magento\OrderIngestion\Plugin;

use Magento\DataExporter\Uuid\ResourceModel\UuidResource;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Model\AbstractModel as Order;
use Magento\OrderIngestion\Api\Data\ExternalOrderInterface;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;
use Magento\OrderIngestion\Service\ExternalOrderService;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

class AssignUuidFromSalesChannel
{
    private const ORDER_TYPE = 'order';
    private const ORDER_ITEM_TYPE = 'order_item';
    private const SKU_KEY = "sku";
    private const TOTAL_AMOUNT_KEY = 'totalAmount';
    private const UUID_KEY = 'uuid';
    private const ENTITY_ID_KEY = 'entity_id';
    private const TYPE_KEY = 'type';

    /**
     * @var ExternalOrderService
     */
    private ExternalOrderService $externalOrderService;

    /**
     * @var UuidResource
     */
    private $salesOrderResource;

    /**
     * @var OrderIngestionLoggerInterface
     */
    private $logger;

    /**
     * @param ExternalOrderService $externalOrderService
     * @param UuidResource $salesOrderResource
     * @param OrderIngestionLoggerInterface $logger
     */
    public function __construct(
        ExternalOrderService          $externalOrderService,
        UuidResource                  $salesOrderResource,
        OrderIngestionLoggerInterface $logger
    )
    {
        $this->externalOrderService = $externalOrderService;
        $this->salesOrderResource = $salesOrderResource;
        $this->logger = $logger;
    }

    /**
     * @param OrderResource $subject
     * @param OrderResource $result
     * @param Order $order
     * @return OrderResource
     *
     * @throws \Exception
     */
    public function afterSave(OrderResource $subject, OrderResource $result, Order $order): OrderResource
    {
        try {
            $ingestedOrder = $this->findChannelOrder($order);
            if ($ingestedOrder === null) {
                return $result;
            }

            $this->updateOrderUuid($order->getId(), $ingestedOrder->getOrderId());
            $this->updateOrderItemsUuid($order->getItems(), $this->getExternalOrderItemsData($ingestedOrder));
        } catch (\Throwable  $e) {
            $error = \Safe\sprintf("Assignation of order and items UUID failed: %s", $e->getMessage());
            $this->logger->error($error);
        }
        return $result;
    }

    /**
     * @param array $entityIds
     * @param string $type
     * @return array
     */
    private function generateBulkData(array $entityIds, string $type): array
    {
        $data = [];
        foreach ($entityIds as $entityId => $uuid) {
            $data[$entityId] = [
                self::ENTITY_ID_KEY => $entityId,
                self::UUID_KEY => $uuid,
                self::TYPE_KEY => $type
            ];
        }
        return $data;
    }

    /**
     * @param array $data
     * @param string $type
     * @param array $entityIds
     * @return void
     */
    private function saveUuid(array $data, string $type, array $entityIds): void
    {
        try {
            $this->salesOrderResource->saveBulk($data);
        } catch (AlreadyExistsException $e) {
            // handle existing uuid
            $this->logger->info(\Safe\sprintf("Assign uuid: %s", $e->getMessage()));
        } catch (\Throwable $e) {
            $this->logger->error(
                \sprintf('Failed to assign UUID for type: %s, ids: %s', $type, implode(',', $entityIds)),
                ['exception' => $e]
            );
        }
    }

    /**
     * @param Order $order
     * @param ExternalOrderInterface $externalOrder
     * @return void
     */
    private function updateOrderUuid(string $orderId, string $externalOrderId): void
    {
        $entityIds = [$orderId => $externalOrderId];
        $data = $this->generateBulkData($entityIds, self::ORDER_TYPE);
        $this->saveUuid($data, self::ORDER_TYPE, $entityIds);
    }

    /**
     * @param ExternalOrderInterface $ingestedOrder
     * @return array
     * @throws \Safe\Exceptions\JsonException
     */
    private function getExternalOrderItemsData(ExternalOrderInterface $ingestedOrder): array
    {
        $result = [];
        $channelOrderData = \Safe\json_decode($ingestedOrder->getOrderData());
        foreach ($channelOrderData->items as $item) {
            $result[] = [
                self::SKU_KEY => $item->sku,
                self::TOTAL_AMOUNT_KEY => $item->totalAmount,
                self::UUID_KEY => $item->itemId->id
            ];
        }

        return $result;
    }

    /**
     * @param array $entityIds
     * @return void
     */
    private function saveOrderItemsUuid(array $entityIds): void
    {
        $data = $this->generateBulkData($entityIds, self::ORDER_ITEM_TYPE);
        $this->saveUuid($data, self::ORDER_ITEM_TYPE, $entityIds);
    }

    /**
     * @param Order $order
     * @return ExternalOrderInterface|null
     */
    private function findChannelOrder(Order $order): ?ExternalOrderInterface
    {
        $externalOrderId = $order->getPayment()->getPoNumber();
        $storeCode = $order->getStore()->getCode();

        if (null === $externalOrderId) {
            return null;
        }

        return $this->externalOrderService->getExternalOrderByStoreCodeAndExternalId($storeCode, $externalOrderId);
    }

    /**
     * @param $orderItems
     * @param array $orderItemsUUIDs
     * @return void
     */
    private function updateOrderItemsUuid($orderItems, array $externalOrderItemsData): void
    {
        $entityIds = [];
        foreach ($orderItems as $orderItem) {
            foreach ($externalOrderItemsData as $key => $item) {
                if ($orderItem->getSku() === $item[self::SKU_KEY]
                    && $orderItem->getBaseRowTotalInclTax() === $item[self::TOTAL_AMOUNT_KEY]) {
                    $entityIds[$orderItem->getItemId()] = $item[self::UUID_KEY];
                    unset($externalOrderItemsData[$key]);
                    break;
                }
            }
        }
        if (!empty($entityIds)) {
            $this->saveOrderItemsUuid($entityIds);
        }
    }
}
