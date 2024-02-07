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

use Madkting\Connect\Helper\Product as MadktingProductHelper;
use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\ProcessedFeed;
use Madkting\Connect\Model\ProcessedFeedFactory;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Madkting\MadktingClient;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\StoreRepositoryInterface;

/**
 * Class AbstractStockUpdate
 * @package Madkting\Connect\Observer
 */
abstract class AbstractStockUpdate implements ObserverInterface
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
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var MadktingProductHelper
     */
    protected $madktingProductHelper;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var ProcessedFeedFactory
     */
    protected $feedFactory;

    /**
     * @var ProductTaskQueueFactory
     */
    protected $productTaskQueueFactory;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * QueueProductSave constructor
     * @param Config $config
     * @param ProductFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     * @param MadktingProductHelper $madktingProductHelper
     * @param StoreRepositoryInterface $storeRepository
     * @param ProcessedFeedFactory $feedFactory
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     * @param MadktingLogger $logger
     */
    public function __construct(
        Config $config,
        ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        MadktingProductHelper $madktingProductHelper,
        StoreRepositoryInterface $storeRepository,
        ProcessedFeedFactory $feedFactory,
        ProductTaskQueueFactory $productTaskQueueFactory,
        MadktingLogger $logger
    ) {
        $this->madktingConfig = $config;
        $this->productFactory = $productFactory;
        $this->madktingProductHelper = $madktingProductHelper;
        $this->storeRepository = $storeRepository;
        $this->feedFactory = $feedFactory;
        $this->productTaskQueueFactory = $productTaskQueueFactory;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
    }

    /**
     * Get product id
     * @param int $productId
     */
    protected function updateStock($productId) {
        if (!empty($productId)) {
            /* Check for stock synchronization */
            $disabledAttributesSync = $this->madktingConfig->getAttributesDisabledSynchronization();
            if (!in_array('stock', $disabledAttributesSync)) {
                /** @var Product $product */
                $product = $this->productFactory->create()->load($productId, 'magento_product_id');

                if (!empty($product->getMadktingProductId())) {
                    try {
                        $storeId = $this->madktingConfig->getSelectedStore();
                        $websiteId = $this->storeRepository->getById($storeId)->getWebsiteId();

                        /* Get stock */
                        try {
                            $magentoProduct = $this->productRepository->getById($productId, false, $storeId);
                            $stock = $this->madktingProductHelper->getProductStock($magentoProduct->getSku());
                        } catch (\Exception $e) {
                            throw new LocalizedException(__('Error getting product %1 stock, %2', $productId, $e->getMessage()));
                        }

                        /* Get Madkting token */
                        $token = $this->madktingConfig->getMadktingToken();
                        if ($token) {
                            $client = new MadktingClient(['token' => $token]);

                            /* Product */
                            switch ($product->getMadktingType()) {
                                case Product::TYPE_PRODUCT:
                                    $service = $client->serviceProduct();
                                    $location = $service->put([
                                        'shop_pk' => $product->getMadktingStoreId(),
                                        'products' => [
                                            'pk' => $product->getMadktingProductId(),
                                            'stock' => $stock
                                        ]
                                    ]);
                                    break;
                                case Product::TYPE_VARIATION:
                                    $service = $client->serviceProductVariation();
                                    $location = $service->put([
                                        'shop_pk' => $product->getMadktingStoreId(),
                                        'product_pk' => $product->getMadktingParentId(),
                                        'variations' => [
                                            'pk' => $product->getMadktingProductId(),
                                            'stock' => $stock
                                        ]
                                    ]);
                                    break;
                            }

                            if (!empty($location)) {

                                /* Get feed ID */
                                preg_match('/feeds\/([\w\-]+)\/?/', $location, $match);
                                $feedId = empty($match[1]) ?: $match[1];

                                /* Save feed information */
                                $feed = $this->feedFactory->create()->setData([
                                    'feed_id' => $feedId,
                                    'event' => ProcessedFeed::EVENT_PRODUCT,
                                    'location' => $location
                                ])->save();

                                if (!empty($feed->getId())) {
                                    $attributes = json_encode(['stock' => $stock]);

                                    $this->productTaskQueueFactory->create()
                                        ->setProductId($productId)
                                        ->setTaskType($product->getMadktingType())
                                        ->setAction(ProductTaskQueue::ACTION_UPDATE)
                                        ->setMadktingAttributes($attributes)
                                        ->setFeedId($feed->getId())
                                        ->setFeedPosition(0)
                                        ->startTask();
                                }
                            }

                            /* Add update task as a backup */
                            try {
                                $this->productTaskQueueFactory->create()->addData([
                                    'product_id' => $productId,
                                    'task_type' => $product->getMadktingType(),
                                    'action' => ProductTaskQueue::ACTION_UPDATE
                                ])->save();

                                $product->setStatus(Product::STATUS_UPDATING)->save();
                            } catch (\Exception $e) {}
                        }
                    } catch (\Exception $e) {
                        $this->logger->debug($e->getMessage());
                    }
                }
            }
        }
    }
}
