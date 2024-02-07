<?php
/**
 * Copyright © Adobe, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesChannels\Model\Logging;

use Monolog\Logger;

/**
 * Log error message for error log level Logger::INFO and higher
 */
class Base extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/channel-manager.log';

    /**
     * @var int
     */
    protected $loggerType = Logger::INFO;
}
