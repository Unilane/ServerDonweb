<?php

namespace Magento\OrderIngestion\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\OrderIngestion\Api\Data\ExternalOrderInterface;
use Magento\OrderIngestion\Model\ResourceModel\ExternalOrder as ExternalOrderResource;

class ExternalOrder extends AbstractModel implements ExternalOrderInterface
{
    protected function _construct()
    {
        $this->_init(ExternalOrderResource::class);
    }

    public function getId(): ?int
    {
        return $this->getData(ExternalOrderInterface::ID_COLUMN);
    }

    public function setExternalOrderId(string $externalOrderId): void
    {
        $this->setData(ExternalOrderInterface::EXTERNAL_ORDER_ID_COLUMN, $externalOrderId);
    }

    public function getExternalOrderId(): string
    {
        return $this->getData(ExternalOrderInterface::EXTERNAL_ORDER_ID_COLUMN);
    }

    public function setCommerceOrderId(string $commerceOrderId): void
    {
        $this->setData(ExternalOrderInterface::COMMERCE_ORDER_ID_COLUMN, $commerceOrderId);
    }

    public function getCommerceOrderId(): ?string
    {
        return $this->getData(ExternalOrderInterface::COMMERCE_ORDER_ID_COLUMN);
    }

    public function setStoreViewCode(string $storeViewCode): void
    {
        $this->setData(ExternalOrderInterface::STORE_VIEW_CODE_COLUMN, $storeViewCode);
    }

    public function getStoreViewCode(): string
    {
        return $this->getData(ExternalOrderInterface::STORE_VIEW_CODE_COLUMN);
    }

    public function setStatus(string $status): void
    {
        $this->setData(ExternalOrderInterface::STATUS_COLUMN, $status);
    }

    public function getStatus(): string
    {
        return $this->getData(ExternalOrderInterface::STATUS_COLUMN);
    }

    public function setCreatedAt($createdAt): void
    {
        $this->setData(ExternalOrderInterface::CREATED_AT_COLUMN, $createdAt);
    }

    public function getCreatedAt()
    {
        return $this->getData(ExternalOrderInterface::CREATED_AT_COLUMN);
    }

    public function setUpdatedAt($updatedAt): void
    {
        $this->setData(ExternalOrderInterface::UPDATED_AT_COLUMN, $updatedAt);
    }

    public function getUpdatedAt()
    {
        return $this->getData(ExternalOrderInterface::UPDATED_AT_COLUMN);
    }

    public function setOrderData(string $orderData): void
    {
        $this->setData(ExternalOrderInterface::ORDER_DATA_COLUMN, $orderData);
    }

    public function getOrderData(): ?string
    {
        return $this->getData(ExternalOrderInterface::ORDER_DATA_COLUMN);
    }

    public function setOrderId(string $orderId): void
    {
        $this->setData(ExternalOrderInterface::ORDER_ID_COLUMN, $orderId);
    }

    public function getOrderId(): string
    {
        return $this->getData(ExternalOrderInterface::ORDER_ID_COLUMN);
    }

    public function setSalesChannel(string $salesChannel): void
    {
        $this->setData(ExternalOrderInterface::SALES_CHANNEL_COLUMN, $salesChannel);
    }

    public function getSalesChannel(): string
    {
        return $this->getData(ExternalOrderInterface::SALES_CHANNEL_COLUMN);
    }

    public function setNumberOfRetries(int $retries): void
    {
        $this->setData(ExternalOrderInterface::RETRIES_COLUMN, $retries);
    }

    public function getNumberOfRetries(): int
    {
        return $this->getData(ExternalOrderInterface::RETRIES_COLUMN) ?? 0;
    }
}
