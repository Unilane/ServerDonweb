<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\OrderIngestion\Api\Data\ExternalOrderInterface;
use Magento\OrderIngestion\Api\Data\ExternalOrderSearchResultsInterface;

/**
 * External Order repository interface.
 */
interface ExternalOrderRepositoryInterface
{
    /**
     * Lists external orders that match specified search criteria.
     *
     *
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     * @return ExternalOrderSearchResultsInterface External Order search result interface.
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ExternalOrderSearchResultsInterface;

    /**
     * Performs persist operations for a specified order.
     *
     * @param ExternalOrderInterface $externalOrder The order ID.
     * @return ExternalOrderInterface External Order interface.
     */
    public function save(ExternalOrderInterface $externalOrder): ExternalOrderInterface;

    /**
     * Get order by order id
     *
     * @param string $orderId
     * @return ExternalOrderInterface|null
     */
    public function getByOrderId(string $orderId): ?ExternalOrderInterface;

    /** Get order by order commerce id
     *
     * @param string $externalOrderId
     * @return ExternalOrderInterface|null
     */
    public function getByExternalOrderId(string $externalOrderId): ?ExternalOrderInterface;

    /**
     * Remove order by order id
     *
     * @param string $orderId
     * @return bool
     */
    public function removeByOrderId(string $orderId);

    /**
     * Get order by id
     *
     * @param string $orderId
     * @return ExternalOrderInterface
     */
    public function get(string $orderId);
}
