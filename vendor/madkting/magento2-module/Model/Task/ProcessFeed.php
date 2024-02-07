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

namespace Madkting\Connect\Model\Task;

use Madkting\Connect\Helper\Images as MadktingImagesHelper;
use Madkting\Connect\Helper\Product as MadktingProductHelper;
use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Config as MadktingConfig;
use Madkting\Connect\Model\ProcessedFeedFactory;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductFactory;
use Madkting\Connect\Model\ProductImage;
use Madkting\Connect\Model\ProductImageFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Madkting\Connect\Model\ResourceModel\ProductTaskQueue\Collection;
use Madkting\MadktingClient;
use Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ProcessFeed
 * @package Madkting\Connect\Model\Task
 */
class ProcessFeed
{
    /**
     * Madkting's feed statuses
     */
    const MADKTING_FEED_COMPLETE = 'Complete';
    const MADKTING_FEED_WAIT = 'Wait';

    /**
     * @var array
     */
    protected $errors = [];

    /**
     * @var ProcessedFeedFactory
     */
    protected $processedFeedFactory;

    /**
     * @var ProductTaskQueueFactory
     */
    protected $productTaskQueueFactory;

    /**
     * @var ProductFactory
     */
    protected $madktingProductFactory;

    /**
     * @var ProductImageFactory
     */
    protected $imageFactory;

    /**
     * @var MadktingProductHelper
     */
    protected $madktingProductHelper;

    /**
     * @var MadktingImagesHelper
     */
    protected $madktingImagesHelper;

    /**
     * @var MagentoProduct
     */
    protected $magentoProduct;

    /**
     * @var MadktingConfig
     */
    protected $madktingConfig;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * ProcessFeed constructor.
     * @param ProcessedFeedFactory $processedFeedFactory
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     * @param ProductFactory $madktingProductFactory
     * @param ProductImageFactory $imageFactory
     * @param MadktingProductHelper $madktingProductHelper
     * @param MadktingImagesHelper $madktingImagesHelper
     * @param MagentoProduct $magentoProduct
     * @param MadktingConfig $madktingConfig
     * @param MadktingLogger $logger
     */
    public function __construct(
        ProcessedFeedFactory $processedFeedFactory,
        ProductTaskQueueFactory $productTaskQueueFactory,
        ProductFactory $madktingProductFactory,
        ProductImageFactory $imageFactory,
        MadktingProductHelper $madktingProductHelper,
        MadktingImagesHelper $madktingImagesHelper,
        MagentoProduct $magentoProduct,
        MadktingConfig $madktingConfig,
        MadktingLogger $logger
    ) {
        $this->processedFeedFactory = $processedFeedFactory;
        $this->productTaskQueueFactory = $productTaskQueueFactory;
        $this->madktingProductFactory = $madktingProductFactory;
        $this->imageFactory = $imageFactory;
        $this->madktingProductHelper = $madktingProductHelper;
        $this->madktingImagesHelper = $madktingImagesHelper;
        $this->magentoProduct = $magentoProduct;
        $this->madktingConfig = $madktingConfig;
        $this->logger = $logger;
    }

    /**
     * @param object $feedData
     * @param string $location
     * @param bool $received
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute($feedData, $location, $received = false)
    {
        if (!empty($feedData)) {

            /* Get feed data */
            if (!empty($feedData->pk) && !empty($feedData->status) && !empty($feedData->result) && !empty($location)) {

                /* Save feed information */
                $feed = $this->processedFeedFactory->create()->load($feedData->pk);

                if (!empty($feed->getId())) {
                    $receivedCount = $feed->getReceivedCount();
                    if ($received) $receivedCount = !empty($receivedCount) ? $receivedCount + 1 : 1;

                    $feed->addData([
                        'status' => $feedData->status,
                        'result' => json_encode($feedData->result),
                        'success_count' => !empty($feedData->result->success) ? count($feedData->result->success) : 0,
                        'error_count' => !empty($feedData->result->errors) ? count($feedData->result->errors) : 0,
                        'critical_count' => !empty($feedData->result->criticals) ? count($feedData->result->criticals) : 0,
                        'received_count' => $receivedCount
                    ])->save();

                    /* Task finish status */
                    switch ($feedData->status) {
                        case self::MADKTING_FEED_COMPLETE:
                            $taskFinishStatus = ProductTaskQueue::STATUS_COMPLETE;
                            break;
                        case self::MADKTING_FEED_WAIT:
                            $taskFinishStatus = ProductTaskQueue::STATUS_PROCESSING;
                            break;
                        default:
                            $taskFinishStatus = ProductTaskQueue::STATUS_ERROR;
                            break;
                    }

                    /**
                     * Get feed type
                     *
                     * @var Collection|ProductTaskQueue[] $tasks
                     */
                    $tasks = $this->productTaskQueueFactory->create()->getCollection()->addFieldToFilter('feed_id', $feedData->pk);
                    $taskType = $tasks->getFirstItem()->getTaskType();
                    $taskAction = $tasks->getFirstItem()->getAction();

                    /* Add default error */
                    foreach ($tasks as $task) {
                        $this->addError($task->getProductId(), __('Unknown error'), Product::STATUS_SYSTEM_ERROR);
                    }

                    /* Process critical */
                    if (!empty($feedData->result->criticals)) {
                        foreach ($tasks as $task) {
                            $this->addError($task->getProductId(), __('Madkting\'s critical error, please try it again'), Product::STATUS_SYSTEM_ERROR);
                        }
                    }

                    /* Process errors */
                    if (!empty($feedData->result->errors)) {
                        foreach ($feedData->result->errors as $error) {
                            try {
                                /* Get product ID */
                                if (!empty($error->product_pk)) $madktingId = $error->product_pk;
                                elseif (!empty($error->id_product)) $madktingId = $error->id_product;

                                if (!empty($error->sku)) {
                                    $productId = $this->magentoProduct->getIdBySku($error->sku);
                                } elseif (!empty($madktingId)){
                                    $productId = $this->madktingProductFactory->create()->load($madktingId, 'madkting_product_id')->getMagentoProductId();
                                } else {
                                    continue;
                                }

                                if (is_array($error->message)) {
                                    $errorMessage = '';
                                    foreach ($error->message as $message) {
                                        $errorMessage .= $message . ' | ';
                                    }
                                } else {
                                    $errorMessage = $error->message;
                                }

                                if (!empty($errorMessage)) {

                                    /* Image type */
                                    if ($taskType == ProductTaskQueue::TYPE_IMAGE) {
                                        if (stripos($errorMessage, 'Imagen no encontrada') !== false) {
                                            if (!empty($error->image_pk)) {
                                                $image = $this->imageFactory->create()->load($error->image_pk, 'madkting_image_id');

                                                if (!empty($image->getId())) {
                                                    $image->delete();

                                                    /* Remove generic error */
                                                    $this->removeError($productId);

                                                    /* Update images positions */
                                                    $this->madktingImagesHelper->queueImagesUpdates($productId, false, true);

                                                    continue;
                                                }
                                            }
                                        }

                                        $errorMessage = __('Image error: %1', $errorMessage);
                                    }

                                    $this->addError($productId, $errorMessage);
                                }
                            } catch (\Exception $e) {
                                $eData = $e->getMessage() . ' ' . $e->getFile() . '(' . $e->getLine() . ')';
                                $logCase = $this->logger->exception($e, __('Madkting webhook general error, %1', $eData));
                                if (!empty($productId)) {
                                    $this->addError($productId, __('Error adding feed error message, check log registry #%1 for more details', $logCase));
                                }
                                echo $eData;
                            } catch (\Throwable $t) {
                                $tData = $t->getMessage() . ' ' . $t->getFile() . '(' . $t->getLine() . ')';
                                $logCase = $this->logger->exception($t, __('Madkting webhook code error, %1', $tData));
                                if (!empty($productId)) {
                                    $this->addError($productId, __('Error adding feed error message, check log registry #%1 for more details', $logCase));
                                }
                                echo $tData;
                            }
                        }
                    }

                    /* Process success */
                    if (!empty($feedData->result->success)) {
                        if ($taskType == ProductTaskQueue::TYPE_PRODUCT || $taskType == ProductTaskQueue::TYPE_VARIATION) {
                            foreach ($feedData->result->success as $success) {
                                try {
                                    /* Get product ID */
                                    if (!empty($success->product_pk)) $madktingId = $success->product_pk;
                                    elseif (!empty($success->id_product)) $madktingId = $success->id_product;
                                    else continue;

                                    if (!empty($success->sku)) {
                                        $productId = $this->magentoProduct->getIdBySku($success->sku);
                                        $product = $this->madktingProductFactory->create()->load($productId, 'magento_product_id');
                                    } elseif (!empty($madktingId)) {
                                        $product = $this->madktingProductFactory->create()->load($madktingId, 'madkting_product_id');
                                        $productId = $product->getMagentoProductId();
                                    } else {
                                        continue;
                                    }

                                    /* Process warnings */
                                    if (!empty($success->warning)) {
                                        if (is_array($success->warning)) {
                                            $warning = '';
                                            foreach ($success->warning as $message) {
                                                $warning .= $message . ' | ';
                                            }
                                        } else {
                                            $warning = $success->warning;
                                        }
                                    }

                                    if (!empty($product->getId())) {

                                        switch ($taskAction) {
                                            case ProductTaskQueue::ACTION_CREATE:

                                                /* Get Madkting attributes  */
                                                $attributesSent = $this->productTaskQueueFactory->create()->getCollection()
                                                    ->addFieldToFilter('feed_id', $feedData->pk)
                                                    ->addFieldToFilter('product_id', $productId)
                                                    ->setPageSize(1)
                                                    ->getFirstItem()
                                                    ->getMadktingAttributes();

                                                $product->setMadktingProductId($madktingId)
                                                    ->setMadktingAttributes($attributesSent)
                                                    ->setStatus(!empty($warning) ? Product::STATUS_WARNING : Product::STATUS_SYNCHRONIZED)
                                                    ->setStatusMessage(!empty($warning) ? trim($warning, ' | ') : null)
                                                    ->save();

                                                if ($taskType == ProductTaskQueue::TYPE_PRODUCT) {
                                                    if ($product->getHasVariations()) {

                                                        /* Get Madkting token */
                                                        $token = $this->madktingConfig->getMadktingToken();
                                                        if (!$token) {
                                                            throw new LocalizedException(__('There is no Yuju token information'));
                                                        }
                                                        $client = new MadktingClient(['token' => $token]);
                                                        $variationService = $client->serviceProductVariation();
                                                        $variations = $variationService->search(array(
                                                            'shop_pk' => $product->getMadktingStoreId(),
                                                            'product_pk' => $product->getMadktingProductId()
                                                        ));

                                                        /* Set variation information */
                                                        if (!empty($variations)) {
                                                            foreach ($variations as $variation) {
                                                                if (!empty($variation->sku)) {

                                                                    /* Get variation ID */
                                                                    $variationId = $this->magentoProduct->getIdBySku($variation->sku);
                                                                    $variationModel = $this->madktingProductFactory->create()->load($variationId, 'magento_product_id');
                                                                    if (!empty($variationModel->getId())) {
                                                                        $variationModel->setMadktingProductId($variation->pk)
                                                                            ->setMadktingParentId($madktingId)
                                                                            ->setStatus(!empty($warning) ? Product::STATUS_PARENT_WARNING : Product::STATUS_SYNCHRONIZED)
                                                                            ->setStatusMessage(!empty($warning) ? trim($warning, ' | ') : null)
                                                                            ->save();
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }

                                                    /* Save images data */
                                                    if (!empty($success->images)) {
                                                        foreach ($success->images as $image) {
                                                            $imageModel = $this->imageFactory->create()->getCollection()
                                                                ->addFieldToFilter('magento_product_id', $productId)
                                                                ->addFieldToFilter('magento_image_url', $image->url_source)
                                                                ->setPageSize(1)
                                                                ->getFirstItem();

                                                            if (!empty($imageModel->getId())) {
                                                                $imageModel
                                                                    ->setMadktingImageId($image->image_pk)
                                                                    ->setMadktingProductId($madktingId)
                                                                    ->setPosition($image->position)
                                                                    ->save();
                                                            }
                                                        }
                                                    }
                                                }
                                                break;
                                            case ProductTaskQueue::ACTION_UPDATE:

                                                /* Get Madkting attributes  */
                                                $lastAttributes = $product->getMadktingAttributes();
                                                $attributesSent = $this->productTaskQueueFactory->create()->getCollection()
                                                    ->addFieldToFilter('feed_id', $feedData->pk)
                                                    ->addFieldToFilter('product_id', $productId)
                                                    ->setPageSize(1)
                                                    ->getFirstItem()
                                                    ->getMadktingAttributes();

                                                $lastAttributesArray = json_decode($lastAttributes, true);
                                                $attributesSentArray = json_decode($attributesSent, true);
                                                if (!empty($lastAttributesArray) && is_array($lastAttributesArray) && !empty($attributesSentArray) && is_array($attributesSentArray)) {
                                                    $productAttributes = json_encode(array_merge($lastAttributesArray, $attributesSentArray));
                                                } else {
                                                    $productAttributes = $attributesSent;
                                                }

                                                $product->setMadktingAttributes($productAttributes)
                                                    ->setStatus(!empty($warning) ? Product::STATUS_WARNING : Product::STATUS_SYNCHRONIZED)
                                                    ->setStatusMessage(!empty($warning) ? trim($warning, ' | ') : null)
                                                    ->save();

                                                /* Check images changes */
                                                $variation = $taskType == ProductTaskQueue::TYPE_VARIATION;
                                                $this->madktingImagesHelper->queueImagesUpdates($productId, $variation);

                                                break;
                                            case ProductTaskQueue::ACTION_DELETE:
                                                $product->delete();

                                                if ($taskType == ProductTaskQueue::TYPE_PRODUCT) {
                                                    if ($product->getHasVariations()) {
                                                        $variations = $this->madktingProductHelper->getVariationsId($product->getMagentoProductId());
                                                        foreach ($variations as $variation) {
                                                            $this->madktingProductFactory->create()->load($variation, 'magento_product_id')->delete();
                                                        }
                                                    }
                                                }
                                                break;
                                        }

                                        /* Remove generic error */
                                        $this->removeError($productId);
                                    }
                                } catch (\Exception $e) {
                                    $eData = $e->getMessage() . ' ' . $e->getFile() . '(' . $e->getLine() . ')';
                                    $logCase = $this->logger->exception($e, __('Madkting webhook general error, %1', $eData));
                                    if (!empty($productId)) {
                                        $this->addError($productId, __('Error processing product feed, check log registry #%1 for more details', $logCase));
                                    }
                                    echo $eData;
                                } catch (\Throwable $t) {
                                    $tData = $t->getMessage() . ' ' . $t->getFile() . '(' . $t->getLine() . ')';
                                    $logCase = $this->logger->exception($t, __('Madkting webhook code error, %1', $tData));
                                    if (!empty($productId)) {
                                        $this->addError($productId, __('Error processing product feed, check log registry #%1 for more details', $logCase));
                                    }
                                    echo $tData;
                                }
                            }
                        } elseif ($taskType == ProductTaskQueue::TYPE_IMAGE) {
                            $madktingProductId = '';
                            $warning = '';
                            $feedError = false;
                            foreach ($feedData->result->success as $success) {
                                try {
                                    /* Get product ID */
                                    if (!empty($success->product_pk)) $madktingProductId = $success->product_pk;
                                    elseif (!empty($success->id_product)) $madktingProductId = $success->id_product;
                                    elseif (empty($madktingProductId)) continue;

                                    /* Get image ID */
                                    if (!empty($success->image_pk)) $madktingImageId = $success->image_pk;
                                    elseif (!empty($success->id_image)) $madktingImageId = $success->id_image;
                                    else continue;

                                    /* Process warnings */
                                    if (!empty($success->warning)) {
                                        if (is_array($success->warning)) {
                                            foreach ($success->warning as $message) {
                                                $warning .= $message . ' | ';
                                            }
                                        } else {
                                            $warning .= $success->warning . ' | ';
                                        }
                                    }

                                    switch ($taskAction) {
                                        case ProductTaskQueue::ACTION_CREATE:

                                            /** @var ProductImage $image */
                                            $image = $this->imageFactory->create()->getCollection()
                                                ->addFieldToFilter('madkting_product_id', $madktingProductId)
                                                ->addFieldToFilter('magento_image_url', $success->url_source)
                                                ->setPageSize(1)
                                                ->getFirstItem();

                                            if (!empty($image->getId())) {
                                                $position = isset($success->position) ? $success->position : 0;
                                                $image->setMadktingImageId($madktingImageId)
                                                    ->setPosition($position)
                                                    ->save();
                                            }
                                            break;
                                        case ProductTaskQueue::ACTION_UPDATE:

                                            if (isset($success->position)) {
                                                /** @var ProductImage $image */
                                                $image = $this->imageFactory->create()->load($madktingImageId, 'madkting_image_id');

                                                if (!empty($image->getId())) {
                                                    $image->setPosition($success->position)->save();
                                                }
                                            }
                                            break;
                                        case ProductTaskQueue::ACTION_DELETE:

                                            $image = $this->imageFactory->create()->load($madktingImageId, 'madkting_image_id');

                                            if (!empty($image->getId())) {
                                                $image->delete();
                                            }
                                            break;
                                    }
                                } catch (\Exception $e) {
                                    $eData = $e->getMessage() . ' ' . $e->getFile() . '(' . $e->getLine() . ')';
                                    $logCase = $this->logger->exception($e, __('Madkting webhook general error, %1', $eData));
                                    $feedError = true;
                                    echo $eData;
                                } catch (\Throwable $t) {
                                    $tData = $t->getMessage() . ' ' . $t->getFile() . '(' . $t->getLine() . ')';
                                    $logCase = $this->logger->exception($t, __('Madkting webhook code error, %1', $tData));
                                    $feedError = true;
                                    echo $tData;
                                }
                            }

                            /** @var Product $product */
                            $product = $this->madktingProductFactory->create()->load($madktingProductId, 'madkting_product_id');

                            if (!empty($product->getId())) {
                                /* Set status */
                                $product->setStatus(!empty($warning) ? Product::STATUS_WARNING : Product::STATUS_SYNCHRONIZED)
                                    ->setStatusMessage(!empty($warning) ? trim($warning, ' | ') : null)
                                    ->save();

                                if ($product->getHasVariations()) {

                                    /** @var Product[] $variations */
                                    $variations = $this->madktingProductFactory->create()->getCollection()
                                        ->addFieldToFilter('madkting_parent_id', $madktingProductId);

                                    if (!empty($warning)) {
                                        foreach ($variations as $variation) {
                                            $variation->setStatus(Product::STATUS_PARENT_WARNING)
                                                ->setStatusMessage(trim($warning, ' | '))
                                                ->save();
                                        }
                                    } else {
                                        foreach ($variations as $variation) {
                                            if ($variation->getStatus() == Product::STATUS_PARENT_ERROR) {
                                                $variation->setStatus(Product::STATUS_SYNCHRONIZED)
                                                    ->setStatusMessage(null)
                                                    ->save();
                                            }
                                        }
                                    }
                                }

                                /* Remove generic error */
                                if (!empty($feedError)) {
                                    $this->addError($product->getMagentoProductId(), __('Error processing images feed, check log registry #%1 for more details', $logCase));
                                } else {
                                    $this->removeError($product->getMagentoProductId());
                                }

                                /* Update images positions in create or delete action */
                                if ($taskAction == ProductTaskQueue::ACTION_CREATE || $taskAction == ProductTaskQueue::ACTION_DELETE) {
                                    $this->madktingImagesHelper->queueImagesUpdates($product->getMagentoProductId(), false, true);
                                }
                            }
                        }
                    }

                    /* Process errors */
                    $this->processErrors();

                    /* Close tasks */
                    foreach ($tasks as $task) {
                        $task->finishTask($taskFinishStatus);

                        if ($taskType == ProductTaskQueue::TYPE_PRODUCT) {

                            /* Close variations task */
                            $variations = $this->madktingProductHelper->getVariationsId($task->getProductId());
                            foreach ($variations as $variation) {

                                /** @var ProductTaskQueue $varitionTask  */
                                $variationTask = $this->productTaskQueueFactory->create()->getCollection()
                                    ->addFieldToFilter('product_id', $variation)
                                    ->addFieldToFilter('status', ProductTaskQueue::STATUS_PROCESSING)
                                    ->addFieldToFilter('action', $task->getAction())
                                    ->setPageSize(1)
                                    ->getFirstItem();
                                if (!empty($variationTask->getId())) {
                                    $variationTask->finishTask($taskFinishStatus);
                                }
                            }
                        }
                    }
                } else {
                    throw new InputException(__('Feed no registered %1', $location));
                }
            } else {
                throw new InputException(__('Feed data error %1', !empty($location) ? $location : __('No location info')));
            }
        } else {
            throw new InputException(__('Feed data missing'));
        }
    }

    /**
     * Add error data
     *
     * @param int $productId
     * @param string $message
     * @param int $type
     */
    protected function addError($productId, $message, $type = Product::STATUS_ERROR)
    {
        $this->errors[$productId] = [
            'message' => $message,
            'type' => $type
        ];
    }

    /**
     * Remove error data
     *
     * @param int $productId
     */
    protected function removeError($productId)
    {
        unset($this->errors[$productId]);
    }

    /**
     * Process errors if exists
     */
    protected function processErrors()
    {
        if (!empty($this->errors)) {
            foreach ($this->errors as $productId => $error) {

                /* Add feed info to product */
                $madktingProduct = $this->madktingProductFactory->create()->load($productId, 'magento_product_id');

                if ($madktingProduct->getId()) {
                    $madktingProduct->setStatus($error['type'])
                        ->setStatusMessage(trim($error['message'], ' | '))
                        ->save();
                }

                /* Variations errors */
                $variations = $this->madktingProductHelper->getVariationsId($productId);
                foreach ($variations as $variation) {
                    $variationInfo = $this->madktingProductFactory->create()->load($variation, 'magento_product_id');
                        if (!empty($variationInfo->getId())) {
                            $variationInfo->setStatus(Product::STATUS_PARENT_ERROR)
                                ->setStatusMessage(trim($error['message'], ' | '))
                                ->save();
                        }
                }
            }
        }
    }
}
