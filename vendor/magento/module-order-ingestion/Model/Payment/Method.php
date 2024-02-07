<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class Method (3rd party channel payment)
 */
class Method extends AbstractMethod
{
    const PAYMENT_METHOD_OFFLINE_CHANNEL = 'offline_channel_payment';

    /**
     * @var string $_code
     */
    protected $_code = self::PAYMENT_METHOD_OFFLINE_CHANNEL;

    /**
     * @var string
     */
    protected $_infoBlockType = \Magento\OrderIngestion\Block\Adminhtml\Payment\Info\Channel::class;

    /**
     * @var bool
     */
    protected $_isOffline = true;

    /**
     * @var bool
     */
    protected $_canUseCheckout = false;

    /** @var bool */
    protected $_canUseInternal = false;
}
