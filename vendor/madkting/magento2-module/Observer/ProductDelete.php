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
use Madkting\MadktingClient;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ProductDelete
 * @package Madkting\Connect\Observer
 */
class ProductDelete implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $madktingConfig;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * QueueProductSave constructor
     * @param Config $config
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Config $config,
        ProductFactory $productFactory
    ) {
        $this->madktingConfig = $config;
        $this->productFactory = $productFactory;
    }

    /**
     * Queue product update
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /* Get product ID */
        $productId = $observer->getEvent()->getProduct()->getId();
        if (!empty($productId)) {

            /** @var Product $product */
            $product = $this->productFactory->create()->load($productId, 'magento_product_id');

            if (!empty($product->getId())) {
                if (!empty($product->getMadktingProductId())) {
                    try {

                        /* Get Madkting token */
                        $token = $this->madktingConfig->getMadktingToken();
                        if ($token) {
                            $client = new MadktingClient(['token' => $token]);

                            /* Product */
                            switch ($product->getMadktingType()) {
                                case Product::TYPE_PRODUCT:
                                    $service = $client->serviceProduct();
                                    $service->delete([
                                        'shop_pk' => $product->getMadktingStoreId(),
                                        'products' => ['pk' => $product->getMadktingProductId()]
                                    ]);

                                    /* If has variations */
                                    if (!empty($product->getHasVariations())) {

                                        /** @var Product[] $variations */
                                        $variations = $this->productFactory->create()->getCollection()->addFieldToFilter('madkting_parent_id', $product->getMadktingProductId());
                                        foreach ($variations as $variation) {
                                            $variation->delete();
                                        }
                                    }
                                    break;
                                case Product::TYPE_VARIATION:
                                    $service = $client->serviceProductVariation();
                                    $service->delete([
                                        'shop_pk' => $product->getMadktingStoreId(),
                                        'product_pk' => $product->getMadktingParentId(),
                                        'variations' => ['pk' => $product->getMadktingProductId()]
                                    ]);
                                    break;
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }

                /* Delete madkting product registry */
                $product->delete();
            }
        }
    }
}
