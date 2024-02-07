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

namespace Madkting\Connect\Helper;

use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\ProcessedFeed;
use Madkting\Connect\Model\ProcessedFeedFactory;
use Madkting\Connect\Model\Product as MadktingProduct;
use Madkting\Connect\Model\ProductFactory;
use Madkting\Connect\Model\ProductImageFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Madkting\Exception\MadktingException;
use Madkting\MadktingClient;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Madkting\Connect\Helper\Product as ProductHelper;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Images
 * @package Madkting\Connect\Helper
 */
class Images extends AbstractHelper
{
    /**
     * Images processed
     *
     * @var array
     */
    protected $processedImages = [];

    /**
     * @var ProductImageFactory
     */
    protected $productImageFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductTaskQueueFactory
     */
    protected $productTaskQueueFactory;

    /**
     * @var ProcessedFeedFactory
     */
    protected $processedFeedFactory;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * @var Config
     */
    protected $madktingConfig;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * Images constructor
     *
     * @param Context $context
     * @param ProductImageFactory $productImageFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactory $productFactory
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     * @param ProcessedFeedFactory $processedFeedFactory
     * @param ProductHelper $productHelper
     * @param Config $madktingConfig
     * @param MadktingLogger $logger
     */
    public function __construct(
        Context $context,
        ProductImageFactory $productImageFactory,
        ProductRepositoryInterface $productRepository,
        ProductFactory $productFactory,
        ProductTaskQueueFactory $productTaskQueueFactory,
        ProcessedFeedFactory $processedFeedFactory,
        ProductHelper $productHelper,
        Config $madktingConfig,
        MadktingLogger $logger
    ) {
        parent::__construct($context);
        $this->productImageFactory = $productImageFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productTaskQueueFactory = $productTaskQueueFactory;
        $this->processedFeedFactory = $processedFeedFactory;
        $this->productHelper = $productHelper;
        $this->madktingConfig = $madktingConfig;
        $this->logger = $logger;
    }

    /**
     * @param int $productId
     * @param bool $isVariation
     * @param bool $updateVariations
     * @throws LocalizedException
     */
    public function queueImagesUpdates($productId, $isVariation = false, $updateVariations = false)
    {
        /* Check attributes disabled for synchronization */
        $disabledAttributesSync = $this->madktingConfig->getAttributesDisabledSynchronization();
        if (in_array('images', $disabledAttributesSync)) {
            return false;
        }

        $productId = $isVariation ? $this->productHelper->getParentId($productId) : $productId;

        if (!in_array($productId, $this->processedImages)) {

            /* Check previous images petitions */
            $previous = $this->productTaskQueueFactory->create()->getCollection()
                ->addFieldToFilter('product_id', $productId)
                ->addFieldToFilter('status', ['nin' => [ProductTaskQueue::STATUS_COMPLETE, ProductTaskQueue::STATUS_ERROR]])
                ->addFieldToFilter('task_type', ProductTaskQueue::TYPE_IMAGE)
                ->getLastItem()->getData();
            if (!empty($previous)) {
                $this->processedImages[] = $productId;
                return false;
            }

            /**
             * Get madkting product
             *
             * @var MadktingProduct $product
             */
            $product = $this->productFactory->create()->load($productId, 'magento_product_id');

            if (!empty($product->getId())) {
                try {
                    /* Get Magento's product images */
                    $magentoImages = $this->getMagentoImages($productId);

                    if (!empty($magentoImages['product'])) {

                        $imagesToAdd = [];
                        $imagesToDelete = [];
                        $imagesToUpdate = [];

                        /**
                         * Get Madkting client
                         *
                         * @var \Madkting\Product\ProductService $productService
                         */
                        $token = $this->madktingConfig->getMadktingToken();
                        if (!$token) {
                            throw new LocalizedException(__('There is no Yuju token information'));
                        }
                        $client = new MadktingClient(['token' => $token]);
                        $productService = $client->serviceProduct();

                        /* Get Madkting images */
                        $madktingProduct = $productService->get(array(
                            'shop_pk' => $product->getMadktingStoreId(),
                            'product_pk' => $product->getMadktingProductId()
                        ));
                        $madktingImages = isset($madktingProduct->images) ? (array) $madktingProduct->images : [];

                        /* Get matched images */
                        $matchedImages = $this->productImageFactory->create()->getCollection()
                            ->addFieldToFilter('madkting_product_id', $product->getMadktingProductId())
                            ->getData();

                        /* Get images to delete */
                        $madktingPks = array_map(function ($val) {
                            return $val->pk;
                        }, $madktingImages);
                        foreach ($matchedImages as &$matchedImage) {
                            if (!in_array($matchedImage['magento_image_id'], array_column($magentoImages['product'], 'id'))
                                || !in_array($matchedImage['madkting_image_id'], $madktingPks)
                                || empty($matchedImage['madkting_image_id'])) {
                                $this->productImageFactory->create()->load($matchedImage['image_id'])->delete();
                                $matchedImage['magento_image_id'] = '';
                                $matchedImage['madkting_image_id'] = '';
                            }
                        }
                        foreach ($madktingImages as $madktingImage) {
                            if (!in_array($madktingImage->pk, array_column($matchedImages, 'madkting_image_id'))) {
                                $imagesToDelete[] = ['pk' => $madktingImage->pk];
                            }
                        }

                        /* Get images to add */
                        if (empty($imagesToDelete)) {
                            foreach ($magentoImages['product'] as $magentoImage) {
                                $key = array_search($magentoImage['id'], array_column($matchedImages, 'magento_image_id'));
                                if ($key === false || empty($matchedImages[$key]['madkting_image_id'])) {

                                    /* Add image registry */
                                    if ($key === false) {
                                        $this->productImageFactory->create()->setData([
                                            'magento_image_url' => $magentoImage['url'],
                                            'magento_product_id' => $productId,
                                            'magento_image_id' => $magentoImage['id'],
                                            'madkting_product_id' => $product->getMadktingProductId()
                                        ])->save();
                                    }

                                    /* Process image in Madkting */
                                    $imagesToAdd[] = ['url' => $magentoImage['url']];
                                }
                            }
                        }

                        /* Get images to update */
                        if (empty($imagesToAdd) && empty($imagesToDelete)) {

                            /* If position change in product */
                            foreach ($madktingImages as $madktingImage) {
                                $magentoImageKey = array_search($madktingImage->pk, array_column($matchedImages, 'madkting_image_id'));
                                $key = array_search($matchedImages[$magentoImageKey]['magento_image_id'], array_column($magentoImages['product'], 'id'));
                                if ($key !== false) {
                                    if ($key != $madktingImage->position) {
                                        $imagesToUpdate['product'][] = [
                                            'pk' => $madktingImage->pk,
                                            'position' => $key
                                        ];

                                        /* Update in variations */
                                        $updateVariations = true;
                                    }
                                }
                            }

                            /* If must update variations */
                            if ($updateVariations) {

                                /* If there are variations images */
                                if (!empty($magentoImages['variations'])) {
                                    foreach ($magentoImages['variations'] as $variationId => $variation) {
                                        $variationPk = $this->productFactory->create()->load($variationId, 'magento_product_id')->getMadktingProductId();

                                        if (!empty($variationPk)) {
                                            foreach ($variation as $imageVariation) {
                                                $key = array_search($imageVariation['url'], array_column($matchedImages, 'magento_image_url'));

                                                if (!empty($matchedImages[$key])) {
                                                    $imagePk = $matchedImages[$key]['madkting_image_id'];

                                                    if (!empty($imagePk)) {
                                                        $imagesToUpdate['variations'][$variationPk][] = ['pk' => $imagePk];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($imagesToAdd) || !empty($imagesToDelete) || !empty($imagesToUpdate)) {
                            /* Product image service */
                            $imageService = $client->serviceProductImage();

                            /* Process images to add */
                            if (!empty($imagesToAdd)) {
                                $location = $imageService->post([
                                    'shop_pk' => $product->getMadktingStoreId(),
                                    'product_pk' => $product->getMadktingProductId(),
                                    'images' => $imagesToAdd
                                ]);

                                $this->createTaskAndFeed($productId, ProductTaskQueue::ACTION_CREATE, $location);
                            }

                            /* Process images to delete */
                            if (!empty($imagesToDelete)) {
                                $location = $imageService->delete([
                                    'shop_pk' => $product->getMadktingStoreId(),
                                    'product_pk' => $product->getMadktingProductId(),
                                    'images' => $imagesToDelete
                                ]);

                                $this->createTaskAndFeed($productId, ProductTaskQueue::ACTION_DELETE, $location);
                            }

                            /* Process images to update */
                            if (!empty($imagesToUpdate)) {

                                /* Positions */
                                if (!empty($imagesToUpdate['product'])) {
                                    $location = $imageService->put([
                                        'shop_pk' => $product->getMadktingStoreId(),
                                        'product_pk' => $product->getMadktingProductId(),
                                        'images' => $imagesToUpdate['product']
                                    ]);

                                    $this->createTaskAndFeed($productId, ProductTaskQueue::ACTION_UPDATE, $location);
                                }

                                /* Variations */
                                if (!empty($imagesToUpdate['variations'])) {

                                    /* Variations image service */
                                    $imageVariationService = $client->serviceProductVariationImage();

                                    foreach ($imagesToUpdate['variations'] as $variationPk => $variationImages) {
                                        if (!empty($variationImages)) {
                                            $location = $imageVariationService->post(array(
                                                'shop_pk' => $product->getMadktingStoreId(),
                                                'product_pk' => $product->getMadktingProductId(),
                                                'variation_pk' => $variationPk,
                                                'images' => $variationImages
                                            ));

                                            /* Get Magento's variation ID */
                                            $variationMagentoId = $this->productFactory->create()->load($variationPk, 'madkting_product_id')->getMagentoProductId();
                                            if (!empty($variationMagentoId)) {
                                                $this->createTaskAndFeed($variationMagentoId, ProductTaskQueue::ACTION_UPDATE, $location);
                                            }
                                        }
                                    }
                                }
                            }

                            /* Set product status */
                            $product->setStatus(MadktingProduct::STATUS_UPDATING_IMAGES)->save();
                        }
                    } else {

                        /* Add empty images error */
                        $message = __('%1 is required', __('Images'));
                        $this->addImagesError($product, $message);
                    }
                } catch (MadktingException $e) {
                    $logCase = $this->logger->exception($e, __('Error Updating Product %1 Images, %2', $productId, $e->getMessage()));

                    /* Add process images error */
                    $message = __('There was an error updating images, check log registry #%1 for more details', $logCase);
                    $this->addImagesError($product, $message);
                } catch (\Exception $e) {
                    $this->logger->exception($e, __('Error Updating Product %1 Images, %2', $productId, $e->getMessage()));
                } catch (\Throwable $e) {
                    $this->logger->exception($e, __('Error Updating Product %1 Images, %2', $productId, $e->getMessage()));
                }
            }

            $this->processedImages[] = $productId;
        }
    }

    /**
     * @param int $productId
     * @return array
     */
    protected function getMagentoImages($productId)
    {
        $storeId = $this->madktingConfig->getSelectedStore();
        /**
         * Get product model
         *
         * @var $product \Magento\Catalog\Model\Product
         */
        $product = $this->productRepository->getById($productId, false, $storeId);

        $imagesProduct = [];

        /* Get product images */
        $gallery = $product->getMediaGalleryImages();
        foreach ($gallery as $image) {
            if (array_search($image->getUrl(), array_column($imagesProduct, 'url')) === false
                && array_search($image->getId(), array_column($imagesProduct, 'id')) === false) {
                $imagesProduct[] = ['url' => $image->getUrl(), 'id' => $image->getId()];
            }
        }

        /* Get variations images */
        $variations = $this->productHelper->getVariationsId($productId);
        if (!empty($variations)) {
            $imagesVariations = [];

            foreach ($variations as $variation) {
                $imagesVariations[$variation] = [];

                /**
                 * Get product model
                 *
                 * @var \Magento\Catalog\Model\Product $variationImages
                 */
                $variationImages = $this->productRepository->getById($variation, false, $storeId)->getMediaGalleryImages();
                foreach ($variationImages as $image) {

                    /* Set variations' images */
                    if (array_search($image->getUrl(), array_column($imagesVariations[$variation], 'url')) === false
                        && array_search($image->getId(), array_column($imagesVariations[$variation], 'id')) === false) {
                        $imagesVariations[$variation][] = ['url' => $image->getUrl(), 'id' => $image->getId()];
                    }

                    /* Add variaitons' images to product */
                    if (array_search($image->getUrl(), array_column($imagesProduct, 'url')) === false
                        && array_search($image->getId(), array_column($imagesProduct, 'id')) === false) {
                        $imagesProduct[] = ['url' => $image->getUrl(), 'id' => $image->getId()];
                    }
                }
            }
        }

        $result = ['product' => $imagesProduct];

        if (!empty($imagesVariations)) {
            $result['variations'] = $imagesVariations;
        }

        return $result;
    }

    /**
     * @param int $productId
     * @param int $action
     * @param string $location
     */
    protected function createTaskAndFeed($productId, $action, $location)
    {
        /* Get feed ID */
        preg_match('/feeds\/([\w\-]+)\/?/', $location, $match);
        $feedId = empty($match[1])?:$match[1];

        /* Save feed information */
        $feed = $this->processedFeedFactory->create()->setData([
            'feed_id' => $feedId,
            'event' => ProcessedFeed::EVENT_PRODUCT,
            'location' => $location
        ])->save();

        if (!empty($feed->getId())) {

            /* Save task information */
            $this->productTaskQueueFactory->create()->setData([
                'product_id' => $productId,
                'task_type' => ProductTaskQueue::TYPE_IMAGE,
                'action' => $action,
                'feed_id' => $feed->getId()
            ])->startTask();
        }
    }

    /**
     * Add images error
     *
     * @param MadktingProduct $product
     * @param string $message
     */
    protected function addImagesError($product, $message)
    {
        $product->setStatus(MadktingProduct::STATUS_ERROR)
            ->setStatusMessage($message)
            ->save();

        /* Add error to variations */
        if (!empty($productPk = $product->getMadktingProductId())) {

            /** @var MadktingProduct[] $variations */
            $variations = $product->getCollection()->addFieldToFilter('madkting_parent_id', $productPk);

            foreach ($variations as $variation) {
                $variation->setStatus(MadktingProduct::STATUS_PARENT_ERROR)
                    ->setStatusMessage($message)
                    ->save();
            }
        }
    }
}
