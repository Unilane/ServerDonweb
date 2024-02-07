<?php
/**
 * Madkting Software (http://www.madkting.com)
 *
 *                                      ..-+::moossso`:.
 *                                    -``         ``ynnh+.
 *                                 .d                 -mmn.
 *     .od/hs..sd/hm.   .:mdhn:.   yo                 `hmn. on     mo omosnomsso oo  .:ndhm:.   .:odhs:.
 *    :hs.h.shhy.d.mh: :do.hd.oh:  /h                `+nm+  dm   ys`  ````mo```` hn :ds.hd.yo: :oh.hd.dh:
 *    ys`   `od`   `h+ sh`    `do  .d`              `snm/`  +s hd`        hd     yy yo`    `sd oh`    ```
 *    hh     sh     +m hs      yy   y-            `+mno`    dkdm          +d     o+ no      ss ys    dosd
 *    y+     ss     oh hdsomsmnmy   ++          .smh/`      om ss.        dh     mn yo      oh sm      hy
 *    sh     ho     ys hs``````yy   .s       .+hh+`         ys   hs.      os     yh os      d+ od+.  ./m/
 *    od     od     od od      od   +y    .+so:`            od     od     od     od od      od  `syssys`
 *                                 .ys .::-`
 *                                o.+`
 *
 * @category Module
 * @package Madkting\Connect
 * @author Carlos Guillermo Jiménez Salcedo <guillermo@madkting.com>
 * @author Israel Calderón Aguilar <israel@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Madkting\Connect\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        /* 1.0.0 version */
        if (version_compare($context->getVersion(), '1.0.0', '<')) {

            /* Create processed feed table */
            $feedTable = $connection->newTable($setup->getTable('madkting_processed_feed'))
                ->addColumn(
                    'feed_id',
                    Table::TYPE_TEXT,
                    255,
                    ['primary' => true, 'nullable' => false]
                )->addColumn(
                    'event',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false],
                    'Feed event type'
                )->addColumn(
                    'location',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Feed response location'
                )->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    20,
                    ['nullable' => false, 'default' => 'Wait']
                )->addColumn(
                    'result',
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Feed response data'
                )->addColumn(
                    'success_count',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => 0]
                )->addColumn(
                    'error_count',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => 0]
                )->addColumn(
                    'critical_count',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => 0]
                )->addColumn(
                    'received_count',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => 0]
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
                )->setComment('Madkting\'s processed feed information');

            $setup->getConnection()->createTable($feedTable);

            /* Create Madkting attribute table */
            $attributeTable = $connection->newTable($setup->getTable('madkting_attribute'))
                ->addColumn(
                    'attribute_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['primary' => true, 'nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'attribute_code',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false]
                )->addColumn(
                    'attribute_label',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false]
                )->addColumn(
                    'attribute_format',
                    Table::TYPE_TEXT,
                    20,
                    ['nullable' => false, 'default' => 'text']
                )->addColumn(
                    'max_length',
                    Table::TYPE_INTEGER
                )->addColumn(
                    'default_value',
                    Table::TYPE_TEXT,
                    50
                )->addColumn(
                    'requirement',
                    Table::TYPE_TEXT,
                    15,
                    ['nullable' => false, 'default' => 'optional']
                )->addColumn(
                    'has_options',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => 0]
                )->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0]
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
                )->addIndex(
                    $setup->getIdxName(
                        'madkting_attribute',
                        'attribute_code',
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    'attribute_code',
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->setComment('Madkting\'s attriutes');

            $setup->getConnection()->createTable($attributeTable);

            /* Create Madkting attribute option table */
            $attributeOptionTable = $connection->newTable($setup->getTable('madkting_attribute_option'))
                ->addColumn(
                    'attribute_option_id',
                    Table::TYPE_TEXT,
                    255,
                    ['primary' => true, 'nullable' => false]
                )->addColumn(
                    'attribute_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'option_value',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false]
                )->addColumn(
                    'option_label',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false]
                )->addColumn(
                    'sort_order',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'default' => 0]
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
                )->addForeignKey(
                    $setup->getFkName(
                        'madkting_attribute_option',
                        'attribute_id',
                        'madkting_attribute',
                        'attribute_id'
                    ),
                    'attribute_id',
                    $setup->getTable('madkting_attribute'),
                    'attribute_id',
                    Table::ACTION_CASCADE
                )->setComment('Yuju\'s attribute option');

            $setup->getConnection()->createTable($attributeOptionTable);

            /* Create Madkting mapping attribute table */
            $attributeMappingTable = $connection->newTable($setup->getTable('madkting_mapping_attribute'))
                ->addColumn(
                    'attribute_mapping_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['primary' => true, 'identity' => true, 'nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'attribute_set_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false]
                )->addColumn(
                    'magento_attribute_id',
                    Table::TYPE_INTEGER
                )->addColumn(
                    'madkting_attribute_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'default_value',
                    Table::TYPE_TEXT,
                    255
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
                )->addForeignKey(
                    $setup->getFkName(
                        'madkting_mapping_attribute',
                        'madkting_attribute_id',
                        'madkting_attribute',
                        'attribute_id'
                    ),
                    'madkting_attribute_id',
                    $setup->getTable('madkting_attribute'),
                    'attribute_id',
                    Table::ACTION_CASCADE
                )->setComment('Attribute Magento - Madkting mapping');

            $setup->getConnection()->createTable($attributeMappingTable);

            /* Create Madkting mapping attribute option table */
            $attributeOptionMappingTable = $connection->newTable($setup->getTable('madkting_mapping_attribute_option'))
                ->addColumn(
                    'attribute_option_mapping_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['primary' => true, 'identity' => true, 'nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'madkting_attribute_option_id',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false]
                )->addColumn(
                    'magento_attribute_option_id',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false]
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
                )->addForeignKey(
                    $setup->getFkName(
                        'madkting_mapping_attribute_option',
                        'madkting_attribute_option_id',
                        'madkting_attribute_option',
                        'attribute_option_id'
                    ),
                    'madkting_attribute_option_id',
                    $setup->getTable('madkting_attribute_option'),
                    'attribute_option_id',
                    Table::ACTION_CASCADE
                )->setComment('Attribute option Magento - Madkting mapping');

            $setup->getConnection()->createTable($attributeOptionMappingTable);

            /* Create Madkting mapping categories table */
            $categoriesMappingTable = $connection->newTable($setup->getTable('madkting_mapping_categories'))
                ->addColumn(
                    'category_mapping_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['primary' => true, 'identity' => true, 'nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'magento_category_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false]
                )->addColumn(
                    'madkting_category_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false]
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
                )->addIndex(
                    $setup->getIdxName(
                        'madkting_mapping_categories',
                        'magento_category_id',
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    'magento_category_id',
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->setComment(
                    'Categories Magento - Categories Madkting mapping'
                );

            $setup->getConnection()->createTable($categoriesMappingTable);

            /* Create Madkting product table */
            $madktingProduct = $connection->newTable($setup->getTable('madkting_product'))
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['primary' => true, 'identity' => true, 'nullable' => false, 'unsigned' => true],
                    'Madkting product table ID'
                )->addColumn(
                    'magento_product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false]
                )->addColumn(
                    'madkting_product_id',
                    Table::TYPE_TEXT,
                    255
                )->addColumn(
                    'madkting_parent_id',
                    Table::TYPE_TEXT,
                    255
                )->addColumn(
                    'magento_store_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'madkting_store_id',
                    Table::TYPE_TEXT,
                    255
                )->addColumn(
                    'madkting_type',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Product type in Madkting: 1 - Product, 2 - Variation'
                )->addColumn(
                    'has_variations',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => false, 'default' => 0]
                )->addColumn(
                    'madkting_attributes',
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Attributes synchronized with Madkting'
                )->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Product sync status in Madkting: 1 - Creation, 2 - Update, 3 - Delete, 4 - Synchronized, 5 - Error, 6 - Warning, 7 - Create Images, 8 - Update Images, 9 - Delete Images, 10 - Parent Error, 11 - Parent Warning, 12 - System Error'
                )->addColumn(
                    'status_message',
                    Table::TYPE_TEXT
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE ]
                )->addIndex(
                    $setup->getIdxName(
                        'madkting_product',
                        'madkting_product_id',
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    'madkting_product_id',
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->addIndex(
                    $setup->getIdxName(
                        'madkting_product',
                        ['magento_product_id', 'magento_store_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['magento_product_id', 'magento_store_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->setComment('Madkting products table');

            $setup->getConnection()->createTable($madktingProduct);

            /* Create Madkting product image table */
            $madktingProductImage = $connection->newTable($setup->getTable('madkting_product_image'))
                ->addColumn(
                    'magento_image_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['primary' => true, 'nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'magento_image_url',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false]
                )->addColumn(
                    'magento_product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'madkting_product_id',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Product Pk in Madkting'
                )->addColumn(
                    'madkting_image_id',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Product image PK in Madkting'
                )->addColumn(
                    'position',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => 0],
                    'Product image position'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE]
                )->addIndex(
                    $setup->getIdxName(
                        'madkting_product_image',
                        'madkting_image_id',
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    'madkting_image_id',
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->addForeignKey(
                    $setup->getFkName(
                        'madkting_product_image',
                        'madkting_product_id',
                        'madkting_product',
                        'madkting_product_id'
                    ),
                    'madkting_product_id',
                    $setup->getTable('madkting_product'),
                    'madkting_product_id',
                    Table::ACTION_CASCADE
                )->setComment(
                    'Images registry'
                );

            $setup->getConnection()->createTable($madktingProductImage);

            /* Create product task queue table */
            $madktingProductTask = $connection->newTable($setup->getTable('madkting_product_task_queue'))
                ->addColumn(
                    'task_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['primary' => true, 'identity' => true, 'nullable' => false, 'unsigned' => true]
                )->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Magento\'s product ID'
                )->addColumn(
                    'task_type',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Task type: 1 - Product, 2 - Variation, 3 - Image'
                )->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => 1],
                    'Task status: 1 - Waiting, 2 - Processing, 3 - Complete, 4 - Error'
                )->addColumn(
                    'action',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true],
                    'Action to do: 1 - Create, 2 - Update, 3 - Delete'
                )->addColumn(
                    'before_action',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => 0],
                    'Action to do before main action: 1 - Create, 2 - Update, 3 - Delete'
                )->addColumn(
                    'after_action',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'unsigned' => true, 'default' => 0],
                    'Action to do after main action: 1 - Create, 2 - Update, 3 - Delete'
                )->addColumn(
                    'selective_sync',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Attributes to Sent, null => all'
                )->addColumn(
                    'madkting_attributes',
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Attributes Sent to Madkting'
                )->addColumn(
                    'feed_id',
                    Table::TYPE_TEXT,
                    255
                )->addColumn(
                    'feed_position',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true]
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT]
                )->addColumn(
                    'started_at',
                    Table::TYPE_TIMESTAMP
                )->addColumn(
                    'finished_at',
                    Table::TYPE_TIMESTAMP
                )->addIndex(
                    $setup->getIdxName(
                        'madkting_product_task_queue',
                        'product_id'
                    ),
                    'product_id'
                )->addForeignKey(
                    $setup->getFkName(
                        'madkting_product_task_queue',
                        'feed_id',
                        'madkting_processed_feed',
                        'feed_id'
                    ),
                    'feed_id',
                    $setup->getTable('madkting_processed_feed'),
                    'feed_id',
                    Table::ACTION_CASCADE
                )->setComment(
                    'Products to be processed in Madkting'
                );

            $setup->getConnection()->createTable($madktingProductTask);
        }

        /* 1.0.3 version */
        if (version_compare($context->getVersion(), '1.0.3', '<')) {

            /* Insert Madkting attributes' new columns */
            $madktingAttributeTable = $setup->getTable('madkting_attribute');

            $columns = [
                'min_num' => [
                    'type' => Table::TYPE_DECIMAL,
                    'scale' => 2,
                    'precision' => 10,
                    'after' => 'max_length',
                    'comment' =>'Min numeric value allowed'
                ],
                'max_num' => [
                    'type' => Table::TYPE_DECIMAL,
                    'scale' => 2,
                    'precision' => 10,
                    'after' => 'min_num',
                    'comment' =>'Max numeric value allowed'
                ],
                'in_variation' => [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => false,
                    'default' => 0,
                    'after' => 'requirement',
                    'comment' =>'If attribute is used in variations'
                ],
                'tooltip' => [
                    'type' => Table::TYPE_TEXT,
                    'after' => 'has_options',
                    'comment' =>'Tooltip'
                ]
            ];

            foreach ($columns as $name => $definition) {
                $connection->addColumn($madktingAttributeTable, $name, $definition);
            }
        }

        /* 1.0.4 version */
        if (version_compare($context->getVersion(), '1.0.4', '<')) {

            /* Change primary key for madkting_product_images table */
            $madktingImagesTable = $setup->getTable('madkting_product_image');
            $connection->dropIndex($madktingImagesTable, 'primary');
            $connection->addColumn(
                $madktingImagesTable,
                'image_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'primary' => true,
                    'identity' => true,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment' => 'Image ID',
                    'after' => 'magento_image_id'
                ]
            );
            $connection->modifyColumn(
                $madktingImagesTable,
                'magento_image_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'after' => 'magento_product_id'
                ]
            );
        }

        $setup->endSetup();
    }
}
