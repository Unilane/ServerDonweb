<?php

namespace Magento\OrderIngestion\Model\ResourceModel\ExternalOrder;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\OrderIngestion\Model\ExternalOrder as ExternalOrderModel;
use Magento\OrderIngestion\Model\ResourceModel\ExternalOrder as ExternalOrderResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(ExternalOrderModel::class, ExternalOrderResourceModel::class);
    }
}
