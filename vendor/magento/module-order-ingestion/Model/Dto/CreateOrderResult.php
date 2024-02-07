<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Model\Dto;

class CreateOrderResult
{
    public const FAIL = 'FAIL';
    public const SUCCESS = 'SUCCESS';

    private string $externalOrderId;
    private string $incrementalOrderId;
    private string $code;
    private string $message;
    private string $commerceOrderId;

    public function __construct(string $externalOrderId, string $incrementalOrderId = null, string $code = self::SUCCESS, string $message = null, string $commerceOrderId = '')
    {
        $this->externalOrderId = $externalOrderId;
        $this->incrementalOrderId = $incrementalOrderId;
        $this->code = $code;
        $this->message = $message;
        $this->commerceOrderId = $commerceOrderId;
    }

    public function getExternalOrderId(): string
    {
        return $this->externalOrderId;
    }

    public function getIncrementalOrderId(): string
    {
        return $this->incrementalOrderId;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCommerceOrderId(): string
    {
        return $this->commerceOrderId;
    }
}
