<?php
/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\OrderIngestion\Block\Adminhtml\Payment\Info;

use Magento\Payment\Block\Info;

class Channel extends Info
{
    protected $_template = 'Magento_OrderIngestion::info/channel.phtml';
}
