<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Service;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface;
use Magento\OrderIngestion\Api\Data\ExternalOrderInterfaceFactory;
use Magento\OrderIngestion\Api\Data\ExternalOrderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\OrderIngestion\Model\Logging\OrderIngestionLoggerInterface;

class ExternalOrderService
{
    /**
     * @var ExternalOrderRepositoryInterface
     */
    private $externalOrderRepository;

    /**
     * @var ExternalOrderInterfaceFactory
     */
    private $externalOrderFactory;

    /**
     * @var OrderIngestionLoggerInterface
     */
    private $logger;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        ExternalOrderRepositoryInterface $externalOrderRepository,
        ExternalOrderInterfaceFactory    $externalOrderFactory,
        SearchCriteriaBuilder            $searchCriteriaBuilder,
        OrderIngestionLoggerInterface    $logger
    )
    {
        $this->externalOrderRepository = $externalOrderRepository;
        $this->externalOrderFactory = $externalOrderFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    public function createExternalOrder(string $orderId, string $externalOrderId, string $storeViewCode, string $salesChannel, string $orderData): ?ExternalOrderInterface
    {
        if ($this->externalOrderRepository->getByOrderId($orderId) !== null) {
            return null;
        }
        $order = $this->externalOrderFactory->create();
        $order->setOrderId($orderId);
        $order->setExternalOrderId($externalOrderId);
        $order->setStoreViewCode($storeViewCode);
        $order->setSalesChannel($salesChannel);
        $order->setStatus(ExternalOrderInterface::STATUS_RECEIVED);
        $order->setOrderData($orderData);

        try {
            return $this->externalOrderRepository->save($order);
        } catch (AlreadyExistsException $e) {
            $this->logger->warning(sprintf("Order %s already created. %s", $externalOrderId, $e->getMessage()));
            return null;
        } catch (\Exception $e) {
            $this->logger->error(sprintf("Error creating order %s. %s", $externalOrderId, $e->getMessage()));
            return null;
        }
    }

    /**
     * @return ExternalOrderInterface[]
     */
    public function getExternalOrders(string $status = ExternalOrderInterface::STATUS_RECEIVED): array
    {
        $criteria = $this->searchCriteriaBuilder
            ->addFilter(ExternalOrderInterface::STATUS_COLUMN, $status, 'eq')
            ->create();

        return $this->externalOrderRepository->getList($criteria)->getItems();
    }

    /**
     * @param string $orderId
     * @return ExternalOrderInterface|null
     */
    public function getExternalOrderById(string $orderId): ?ExternalOrderInterface
    {
        return $this->externalOrderRepository->getByOrderId($orderId);
    }

    public function getExternalOrderByStoreCodeAndExternalId(string $storeCode, string $orderExternalId): ?ExternalOrderInterface
    {
        $criteria = $this->searchCriteriaBuilder
            ->addFilter(ExternalOrderInterface::STORE_VIEW_CODE_COLUMN, $storeCode)
            ->addFilter(ExternalOrderInterface::EXTERNAL_ORDER_ID_COLUMN, $orderExternalId)
            ->create();

        $result = $this->externalOrderRepository->getList($criteria)->getItems();
        return $result ? array_shift($result) : null;
    }

    /**
     * @return ExternalOrderInterface[]
     */
    public function markAsImported(ExternalOrderInterface $order, string $commerceOrderId): ExternalOrderInterface
    {
        $order->setStatus(ExternalOrderInterface::STATUS_IMPORTED);
        $order->setCommerceOrderId($commerceOrderId);

        return $this->externalOrderRepository->save($order);
    }

    /**
     * @return ExternalOrderInterface[]
     */
    public function markAsFail(ExternalOrderInterface $order): ExternalOrderInterface
    {
        $order->setStatus(ExternalOrderInterface::STATUS_FAIL);
        return $this->externalOrderRepository->save($order);
    }

    /**
     * @return ExternalOrderInterface[]
     */
    public function markAsRetry(ExternalOrderInterface $order, int $initialRetries): ExternalOrderInterface
    {
        $order->setStatus(ExternalOrderInterface::STATUS_RETRY);
        $order->setNumberOfRetries($initialRetries);
        return $this->externalOrderRepository->save($order);
    }

    /**
     * @return ExternalOrderInterface[]
     */
    public function decrementNumberOfRetries(ExternalOrderInterface $order): ExternalOrderInterface
    {
        $order->setNumberOfRetries($order->getNumberOfRetries() - 1);
        return $this->externalOrderRepository->save($order);
    }
}
