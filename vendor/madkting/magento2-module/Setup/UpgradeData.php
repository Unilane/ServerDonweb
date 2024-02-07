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
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Tax\Model\ClassModel;

/**
 * Class UpgradeData
 * @package Madkting\Connect\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Madkting's Name
     */
    const MADKTING_NAME = 'Madkting';

    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * InstallData constructor
     *
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        /* 0.0.2 version */
        if (version_compare($context->getVersion(), '0.0.2', '<')) {

            /* Mapping order status table data */
            $orderStatuses = [
                [
                    'status_madkting' => 'payment_required',
                    'status_magento' => Order::STATE_PENDING_PAYMENT,
                    'document' => 'N/A',
                    'create_document' => 0
                ],
                [
                    'status_madkting' => 'pending',
                    'status_magento' => 'pending',
                    'document' => 'N/A',
                    'create_document' => 0
                ],
                [
                    'status_madkting' => 'paid',
                    'status_magento' => Order::STATE_PROCESSING,
                    'document' => 'Invoice',
                    'create_document' => 1
                ],
                [
                    'status_madkting' => 'shipped',
                    'status_magento' => Order::STATE_COMPLETE,
                    'document' => 'Shipment',
                    'create_document' => 1
                ],
                [
                    'status_madkting' => 'delivered',
                    'status_magento' => Order::STATE_COMPLETE,
                    'document' => 'N/A',
                    'create_document' => 0
                ],
                [
                    'status_madkting' => 'failed_delivery',
                    'status_magento' => Order::STATE_COMPLETE,
                    'document' => 'N/A',
                    'create_document' => 0
                ],
                [
                    'status_madkting' => 'refunded',
                    'status_magento' => Order::STATE_CLOSED,
                    'document' => 'Credit Memo',
                    'create_document' => 1
                ],
                [
                    'status_madkting' => 'canceled',
                    'status_magento' => Order::STATE_CANCELED,
                    'document' => 'N/A',
                    'create_document' => 0
                ]
            ];
            $connection->insertMultiple($setup->getTable('madkting_mapping_order_status'), $orderStatuses);

            /* Add Madkting order status to Magento quote and order */
            $quoteSetup = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
            $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

            $attributes = [
                'madkting_status' => ['type' => 'varchar', 'visible' => false, 'length' => 50, 'grid' => true, 'comment' =>'Madkting Order Status'],
                'madkting_marketplace_reference' => ['type' => 'varchar', 'visible' => false, 'length' => 255, 'grid' => true, 'comment' =>'Madkting Marketplace Order Reference']
            ];

            foreach ($attributes as $attributeCode => $attributeParams) {
                $quoteSetup->addAttribute('quote', $attributeCode, $attributeParams);
                $salesSetup->addAttribute('order', $attributeCode, $attributeParams);
            }
        }

        /* 1.0.0 version */
        if (version_compare($context->getVersion(), '1.0.0', '<')) {

            /* Add unique index for madkting_pk on sales_order table */
            $connection->addIndex(
                $setup->getTable('sales_order'),
                $connection->getIndexName(
                    'sales_order',
                    'madkting_pk',
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                'madkting_pk',
                AdapterInterface::INDEX_TYPE_UNIQUE
            );

            /* Madkting tax class */
            $taxTable = $setup->getTable('tax_class');
            $connection->insert(
                $taxTable,
                [
                    'class_name' => self::MADKTING_NAME,
                    'class_type' => ClassModel::TAX_CLASS_TYPE_CUSTOMER
                ]
            );
            $taxClassId = $connection->lastInsertId($taxTable);

            /* Madkting customer group */
            if (!empty($taxClassId)) {
                $connection->insert(
                    $setup->getTable('customer_group'),
                    [
                        'customer_group_code' => self::MADKTING_NAME,
                        'tax_class_id' => $taxClassId
                    ]
                );
            }

            /* Creation of last synchronization date field */
            $connection->insert($setup->getTable('core_config_data'), [
                'path' => 'madkting_synchronization/products/last_sync_date',
                'value' => '0'
            ]);
        }

        /* 1.0.2 version */
        if (version_compare($context->getVersion(), '1.0.2', '<')) {

            /* Update order status mapping data */
            $orderStatusTable = $setup->getTable('madkting_mapping_order_status');
            $rows = [
                'payment_required' => [
                    'document' => null
                ],
                'pending' => [
                    'document' => null
                ],
                'paid' => [
                    'document' => 'invoice'
                ],
                'shipped' => [
                    'document' => 'shipment'
                ],
                'delivered' => [
                    'document' => null
                ],
                'failed_delivery' => [
                    'document' => null
                ],
                'refunded' => [
                    'document' => 'credit_memo'
                ],
                'canceled' => [
                    'document' => 'credit_memo',
                    'create_document' => 1
                ]
            ];
            foreach ($rows as $madktingStatus => $data) {
                $connection->update($orderStatusTable, $data, [
                    'status_madkting = ?' => $madktingStatus
                ]);
            }
        }

        /* 1.0.4 version */
        if (version_compare($context->getVersion(), '1.0.4', '<')) {

            /* Change Order Marketplace pk default value */
            $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
            $salesSetup->getSetup()->getConnection('sales')->modifyColumn(
                $setup->getTable('sales_order'),
                'madkting_marketplace_pk',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => '0',
                    'comment' =>'Madkting Marketplace ID'
                ]
            );
        }

        /* 1.0.5 version */
        if (version_compare($context->getVersion(), '1.0.5', '<')) {

            /* Update Magento config data */
            $configDataTable = $setup->getTable('core_config_data');
            $connection->update(
                $configDataTable,
                ['path' => new \Zend_Db_Expr('REPLACE(path, "madkting_configuration/general", "madkting_general/connection")')],
                ['path LIKE ?' => 'madkting_configuration/general%']
            );
        }

        /* 1.0.31 version */
        if (version_compare($context->getVersion(), '1.0.31', '<')) {

            /* Add Madkting fulfillment to Magento quote and order */
            $quoteSetup = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
            $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

            $attributes = [
                'madkting_fulfillment' => ['type' => 'varchar', 'visible' => false, 'length' => 5, 'grid' => true, 'comment' =>'Madkting Fulfillment Type']
            ];

            foreach ($attributes as $attributeCode => $attributeParams) {
                $quoteSetup->addAttribute('quote', $attributeCode, $attributeParams);
                $salesSetup->addAttribute('order', $attributeCode, $attributeParams);
            }
        }

        $setup->endSetup();
    }
}
