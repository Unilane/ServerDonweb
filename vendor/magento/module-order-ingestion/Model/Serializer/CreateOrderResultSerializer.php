<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Model\Serializer;

use \Magento\OrderIngestion\Model\Dto\CreateOrderResult;

class CreateOrderResultSerializer
{
    /**
     * @param CreateOrderResult[] $createOrderResult
     * @return string
     */
    public function serialize(array $createOrderResults) : string
    {
        $createOrderResultsArray = [];
        foreach ($createOrderResults as $createOrderResult) {
            $createOrderResultsArray[] = $this->createOrderResultToArray($createOrderResult);
        }
        return json_encode($createOrderResultsArray);
    }

    private function createOrderResultToArray(CreateOrderResult $createOrderResult) {
        $arrayValue = [
            'orderId' => $createOrderResult->getExternalOrderId(),
            'code' => $createOrderResult->getCode(),
        ];
        if ($createOrderResult->getIncrementalOrderId() !== null) {
            $arrayValue['commerceOrderId'] = $createOrderResult->getIncrementalOrderId();
        }
        if ($createOrderResult->getMessage() !== null) {
            $arrayValue['message'] = $createOrderResult->getMessage();
        }

        return $arrayValue;
    }
}
