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

namespace Madkting\Connect\Observer;

use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class QueueProductSave
 * @package Madkting\Connect\Observer
 */
class QueueProductSave implements ObserverInterface
{
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
     * QueueProductSave constructor
     * @param Config $config
     * @param ProductFactory $productFactory
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     */
    public function __construct(
        Config $config,
        ProductFactory $productFactory,
        ProductTaskQueueFactory $productTaskQueueFactory
    ) {
        $this->config = $config;
        $this->productFactory = $productFactory;
        $this->productTaskQueueFactory = $productTaskQueueFactory;
    }

    /**
     * Queue product update
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isPermanentSynchronizationEnabled()) {

            /* Get product ID */
            $productId = $observer->getEvent()->getProduct()->getId();
            if (!empty($productId)) {

                /** @var Product $product */
                $product = $this->productFactory->create()->load($productId, 'magento_product_id');

                if (!empty($product->getMadktingProductId())) {
                    try {
                        $this->productTaskQueueFactory->create()->addData([
                            'product_id' => $productId,
                            'task_type' => $product->getMadktingType(),
                            'action' => ProductTaskQueue::ACTION_UPDATE
                        ])->save();

                        $product->setStatus(Product::STATUS_UPDATING)->save();
                    } catch (\Exception $e) {}
                }
            }
        }
    }
}
