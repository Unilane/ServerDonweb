<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Plugin;

class AvoidAggregationBySkuForQuoteItemPlugin
{
    public function afterRepresentProduct($subject, $result) {
        if ($subject->getProduct()->getSeparateByItem() === true) {
            return false;
        }
        return $result;
    }
}
