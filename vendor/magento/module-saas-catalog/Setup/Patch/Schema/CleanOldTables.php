<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SaaSCatalog\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class CleanOldTables implements SchemaPatchInterface
{

    /**
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        private readonly SchemaSetupInterface $schemaSetup
    ) {}

    /**
     * @inheritdoc
     */
    public function apply(): self
    {
        $this->schemaSetup->startSetup();

        $table = $this->schemaSetup->getTable('catalog_product_data_submitted_hash');
        $this->schemaSetup->getConnection()->truncateTable($table);

        $this->schemaSetup->endSetup();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
