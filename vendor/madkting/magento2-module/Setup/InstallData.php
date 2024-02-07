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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Class InstallData
 * @package Madkting\Connect\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * InstallData constructor.
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
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /* Add Madkting order data to Magento quote and order */
        $quoteSetup = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
        $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $attributes = [
            'madkting_pk' => ['type' => 'varchar', 'visible' => false, 'length' => 255, 'grid' => true, 'comment' =>'Madkting Order ID'],
            'madkting_marketplace_pk' => ['type' => 'varchar', 'visible' => false, 'length' => 255, 'grid' => true, 'comment' =>'Madkting Marketplace ID'],
            'madkting_shop_pk' => ['type' => 'varchar', 'visible' => false, 'length' => 255, 'grid' => true, 'comment' =>'Madkting Shop ID']
        ];

        foreach ($attributes as $attributeCode => $attributeParams) {
            $quoteSetup->addAttribute('quote', $attributeCode, $attributeParams);
            $salesSetup->addAttribute('order', $attributeCode, $attributeParams);
        }

        /* Start orders creation date config */
        $setup->getConnection()->insert($setup->getTable('core_config_data'), [
            'path' => 'madkting_synchronization/orders/start_creation_from',
            'value' => date('Y-m-d')
        ]);

        $setup->endSetup();
    }
}
