<?php

namespace Magento\OrderIngestion\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ExternalOrder extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('order_ingestion_external_order', 'id');
    }
}
