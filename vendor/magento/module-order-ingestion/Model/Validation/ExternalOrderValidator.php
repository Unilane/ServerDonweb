<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Model\Validation;

use Magento\Framework\Stdlib\ArrayUtils;
use Magento\OrderIngestion\Exception\ExternalOrderValidationException;

class ExternalOrderValidator
{

    private const VALIDATION_PATHS = [
        'orderId/id',
        'externalId/id',
        'storeViewCode',
        'externalId/salesChannel'
    ];

    private ArrayUtils $arrayUtils;

    public function __construct(ArrayUtils $arrayUtils) {
        $this->arrayUtils = $arrayUtils;
    }

    /**
     * @throws ExternalOrderValidationException
     */
    public function validate(array $externalOrderData) : void
    {
        $missingFields = [];
        $flattenExternalOrderData = $this->arrayUtils->flatten($externalOrderData);
        foreach (self::VALIDATION_PATHS as $validationPath) {
            if (!isset($flattenExternalOrderData[$validationPath])) {
                $missingFields[] = $validationPath;
            }
        }
        if (!empty($missingFields)) {
            throw new ExternalOrderValidationException(
                \Safe\sprintf(
                    "Sales order data is missing mandatory fields [%s]",
                    implode(',', $missingFields)
                )
            );
        }
    }
}
