<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Api\Data;

/**
 * Order service order interface.
 */
interface ExternalOrderInterface
{
    public const ID_COLUMN = 'id';
    public const EXTERNAL_ORDER_ID_COLUMN = 'external_order_id';
    public const COMMERCE_ORDER_ID_COLUMN = 'commerce_order_id';
    public const STATUS_COLUMN = 'status';
    public const RETRIES_COLUMN = 'retries';
    public const STORE_VIEW_CODE_COLUMN = 'store_view_code';
    public const CREATED_AT_COLUMN = 'created_at';
    public const UPDATED_AT_COLUMN = 'updated_at';
    public const ORDER_DATA_COLUMN = 'order_data';
    public const ORDER_ID_COLUMN = 'order_id';
    public const SALES_CHANNEL_COLUMN = 'sales_channel';
    public const STATUS_RECEIVED = 'RECEIVED';
    public const STATUS_IMPORTED = 'IMPORTED';
    public const STATUS_FAIL = 'FAIL';
    public const STATUS_RETRY = 'RETRY';

    /**
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * @param string $orderId
     * @return void
     */
    public function setOrderId(string $orderId): void;

    /**
     * @return string
     */
    public function getOrderId(): string;

    /**
     * @param string $salesChannel
     * @return void
     */
    public function setSalesChannel(string $salesChannel): void;

    /**
     * @return string
     */
    public function getSalesChannel(): string;

    /**
     * @param string $orderExternalId
     * @return void
     */
    public function setExternalOrderId(string $orderExternalId): void;

    /**
     * @return string
     */
    public function getExternalOrderId(): string;

    /**
     * @param string $commerceOrderId
     * @return void
     */
    public function setCommerceOrderId(string $commerceOrderId): void;

    /**
     * @return string
     */
    public function getCommerceOrderId(): ?string;

    /**
     * @param string $storeViewCode
     * @return void
     */
    public function setStoreViewCode(string $storeViewCode): void;

    /**
     * @return string
     */
    public function getStoreViewCode(): string;

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void;

    /**
     * @param int $retries
     * @return void
     */
    public function setNumberOfRetries(int $retries): void;

    /**
     * @return int
     */
    public function getNumberOfRetries(): int;

    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return void
     */
    public function setCreatedAt($createdAt): void;

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt): void;

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $orderData
     * @return void
     */
    public function setOrderData(string $orderData): void;

    /**
     * @return string
     */
    public function getOrderData(): ?string;
}
