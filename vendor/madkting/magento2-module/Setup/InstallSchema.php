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
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Madkting\Connect\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritdoc
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        /* Create order action table */
        $actionsTable = $connection->newTable($setup->getTable('madkting_order_actions'))
            ->addColumn(
                'action_id',
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false, 'unsigned' => true]
            )->addColumn(
                'madkting_pk',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Madkting Order ID'
            )->addColumn(
                'action',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Action Name'
            )->addColumn(
                'done',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 0],
                'If action has been done'
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
            )->setComment('Madkting\'s order actions');

        $setup->getConnection()->createTable($actionsTable);

        /* Create mapping order status table */
        $orderStatusMappingTable = $connection->newTable($setup->getTable('madkting_mapping_order_status'))
            ->addColumn(
                'order_status_id',
                Table::TYPE_INTEGER,
                null,
                ['primary' => true, 'identity' => true, 'nullable' => false, 'unsigned' => true]
            )->addColumn(
                'status_madkting',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false]
            )->addColumn(
                'status_magento',
                Table::TYPE_TEXT,
                50,
                ['nullable' => false]
            )->addColumn(
                'document',
                Table::TYPE_TEXT,
                20,
                [],
                'Document to create with this status'
            )->addColumn(
                'create_document',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 0],
                'If want to create document'
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
            )->setComment('Madkting\'s order status mapping');

        $setup->getConnection()->createTable($orderStatusMappingTable);

        $setup->endSetup();
    }
}
