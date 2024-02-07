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

namespace Madkting\Connect\Plugin;

use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save;
use Magento\Catalog\Helper\Product as MagentoProductHelper;
use Magento\Catalog\Helper\Product\Edit\Action\Attribute;
use Magento\Catalog\Model\Indexer\Product\Flat\Processor as FlatProcessor;
use Magento\Catalog\Model\Indexer\Product\Price\Processor as PriceProcessor;
use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor as StockProcessor;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class QueueProductMassiveChangePlugin
 * @package Madkting\Connect\Plugin
 */
class QueueProductMassiveChangePlugin
{
    /**
     * @var Attribute
     */
    protected $attributeHelper;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductTaskQueueFactory
     */
    protected $productTaskQueueFactory;

    /**
     * QueueProductMassiveChangePlugin constructor
     *
     * @param Action\Context $context
     * @param Attribute $attributeHelper
     * @param Config $config
     * @param ProductFactory $productFactory
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     */
    public function __construct(
        Action\Context $context,
        Attribute $attributeHelper,
        Config $config,
        ProductFactory $productFactory,
        ProductTaskQueueFactory $productTaskQueueFactory
    ) {
        $this->attributeHelper = $attributeHelper;
        $this->config = $config;
        $this->productFactory = $productFactory;
        $this->productTaskQueueFactory = $productTaskQueueFactory;
    }


    /**
     * @param Save $subject
     * @param $result
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function afterExecute(Save $subject, $result)
    {
        if ($this->config->isPermanentSynchronizationEnabled()) {

            /* Get product ID's */
            $productIds = $this->attributeHelper->getProductIds();
            if (!empty($productIds)) {
                foreach ($productIds as $id) {

                    /** @var Product $product */
                    $product = $this->productFactory->create()->load($id, 'magento_product_id');

                    if (!empty($product->getMadktingProductId())) {
                        try {
                            $this->productTaskQueueFactory->create()->addData([
                                'product_id' => $id,
                                'task_type' => $product->getMadktingType(),
                                'action' => ProductTaskQueue::ACTION_UPDATE
                            ])->save();

                            $product->setStatus(Product::STATUS_UPDATING)->save();
                        } catch (\Exception $e) {}
                    }
                }
            }
        }

        return $result;
    }
}
