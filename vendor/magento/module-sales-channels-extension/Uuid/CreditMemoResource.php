<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesChannels\Uuid;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

/**
 * UUID resource model
 */
class CreditMemoResource extends AbstractDb
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_setMainTable('data_exporter_uuid');
    }

    public function getEntityId(string $uuid) : string
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), ['entity_id'])
            ->where('uuid = ?', $uuid)
            ->where('type = ?', 'credit_memo');

        return $connection->fetchOne($select);
    }
}
