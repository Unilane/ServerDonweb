<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Api\Data;

/**
 * Interface for order service orders search results.
 */
interface ExternalOrderSearchResultsInterface
{
    /**
     * Get order list.
     *
     * @return Magento\OrderIngestion\Api\Data\ExternalOrderInterface[]
     */
    public function getItems();

    /**
     * Set order list.
     *
     * @param Magento\OrderIngestion\Api\Data\ExternalOrderInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
