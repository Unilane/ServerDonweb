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

use Madkting\Connect\Helper\Product as MadktinProductHelper;
use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Config as MadktingConfig;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductFactory as MadktingProductFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class ProcessTask
 * @package Madkting\Connect\Model\Task
 */
class ProcessTask
{
    /**
     * @var array
     */
    protected $createFeeds = [];

    /**
     * @var array
     */
    protected $updateFeeds = [];

    /**
     * @var array
     */
    protected $deleteFeeds = [];

    /**
     * @var ProductTaskQueueFactory
     */
    protected $taskQueueFactory;

    /**
     * @var MadktingProductFactory
     */
    protected $madktingProductFactory;

    /**
     * @var MadktinProductHelper
     */
    protected $madktingProductHelper;

    /**
     * @var MadktingConfig
     */
    protected $madktingConfig;

    /**
     * @var CreateFeed
     */
    protected $createFeed;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * ProcessTask constructor
     *
     * @param ProductTaskQueueFactory $taskQueueFactory
     * @param MadktingProductFactory $madktingProductFactory
     * @param MadktinProductHelper $madktinProductHelper
     * @param MadktingConfig $madktingConfig
     * @param CreateFeed $createFeed
     * @param DateTime $dateTime
     * @param MadktingLogger $logger
     */
    public function __construct(
        ProductTaskQueueFactory $taskQueueFactory,
        MadktingProductFactory $madktingProductFactory,
        MadktinProductHelper $madktinProductHelper,
        MadktingConfig $madktingConfig,
        CreateFeed $createFeed,
        DateTime $dateTime,
        MadktingLogger $logger
    ) {
        $this->taskQueueFactory = $taskQueueFactory;
        $this->madktingProductFactory = $madktingProductFactory;
        $this->madktingProductHelper = $madktinProductHelper;
        $this->madktingConfig = $madktingConfig;
        $this->createFeed = $createFeed;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Process next tasks
     *
     * @return int
     */
    public function nextTasks()
    {
        /* Get waiting tasks */
        $taskCount = 0;
        $startTime = $this->dateTime->gmtTimestamp();

        /* Reset actions arrays */
        $this->createFeeds = [];
        $this->updateFeeds = [];
        $this->deleteFeeds = [];

        $maxSeconds = $this->madktingConfig->getMaxTimeGetProducts();
        while ($this->areTaskToDo() &&
            ($startTime + $maxSeconds) > $this->dateTime->gmtTimestamp()) {

            /** @var ProductTaskQueue $task */
            $task = $this->taskQueueFactory->create()->getCollection()
                ->addFieldToFilter('status', ProductTaskQueue::STATUS_WAITING)
                ->setOrder('task_id', Collection::SORT_ORDER_ASC)
                ->setPageSize(1)
                ->getFirstItem();

            if ($task->getId()) {
                $task->startTask();
                $taskData = $task->getData();

                /** @var Product $productModel */
                $productModel = $this->madktingProductFactory->create()->load($taskData['product_id'], 'magento_product_id');

                /* Task type */
                switch ($taskData['task_type']) {

                    /* Product task type */
                    case ProductTaskQueue::TYPE_PRODUCT:
                        $processVariations = false;

                        /* Task action */
                        switch ($taskData['action']) {

                            /* Product create action */
                            case ProductTaskQueue::ACTION_CREATE:

                                $this->createFeeds[] = [
                                    'type' => ProductTaskQueue::TYPE_PRODUCT,
                                    'product' => $taskData['product_id'],
                                    'shop' => $productModel->getMadktingStoreId(),
                                    'taskId' => $taskData['task_id']
                                ];
                                break;

                            /* Product update action */
                            case ProductTaskQueue::ACTION_UPDATE:
                                $madktingId = $productModel->getMadktingProductId();

                                if (!empty($madktingId)) {

                                    $this->updateFeeds[] = [
                                        'type' => ProductTaskQueue::TYPE_PRODUCT,
                                        'product' => $taskData['product_id'],
                                        'productPk' => $madktingId,
                                        'shop' => $productModel->getMadktingStoreId(),
                                        'taskId' => $taskData['task_id']
                                    ];

                                    if ($productModel->getHasVariations()) {
                                        $processVariations = true;
                                    }
                                }
                                break;

                            /* Product delete action */
                            case ProductTaskQueue::ACTION_DELETE:
                                $madktingId = $productModel->getMadktingProductId();

                                if (!empty($madktingId)) {
                                    $this->deleteFeeds[] = [
                                        'type' => ProductTaskQueue::TYPE_PRODUCT,
                                        'product' => $taskData['product_id'],
                                        'productPk' => $madktingId,
                                        'shop' => $productModel->getMadktingStoreId(),
                                        'taskId' => $taskData['task_id']
                                    ];
                                }
                                break;
                        }

                        /* If has variations */
                        if ($productModel->getHasVariations()) {
                            $this->queueVariationsByProduct($taskData['product_id'], $taskData['action'], $processVariations);
                        }

                        break;

                    /* Variation task type */
                    case ProductTaskQueue::TYPE_VARIATION:

                        /* Task action */
                        switch ($taskData['action']) {

                            /* Variation create action */
                            case ProductTaskQueue::ACTION_CREATE:

                                /* Check parent creation */
                                $parentCreation = false;
                                $parentTask = null;
                                $parentId = $this->madktingProductHelper->getParentId($taskData['product_id']);
                                $parentModel = $this->madktingProductFactory->create();
                                $parentMadktingId = $parentModel->load($parentId, 'magento_product_id')->getMadktingProductId();

                                if (empty($parentMadktingId)) {

                                    /** @var ProductTaskQueue $parentTask */
                                    $parentTask = $this->taskQueueFactory->create()->getCollection()
                                        ->addFieldToFilter('product_id', $parentId)
                                        ->addFieldToFilter('status', ['nin' => [ProductTaskQueue::STATUS_COMPLETE, ProductTaskQueue::STATUS_ERROR]])
                                        ->addFieldToFilter('action', $taskData['action'])
                                        ->setPageSize(1)
                                        ->getFirstItem();

                                    if (!empty($parentTask->getId()) && $parentTask->getStatus() == ProductTaskQueue::STATUS_WAITING) {
                                        $parentCreation = true;
                                        $parentTask->startTask();
                                    } elseif (empty($parentTask->getId())) {
                                        $parentCreation = true;

                                        /* Create parent product if no exists */
                                        if (empty($parentModel->getId())) {
                                            $parentModel->setData([
                                                'magento_product_id' => $parentId,
                                                'magento_store_id' => $productModel->getMagentoStoreId(),
                                                'madkting_store_id' => $productModel->getMadktingStoreId(),
                                                'madkting_type' => Product::TYPE_PRODUCT,
                                                'has_variations' => true,
                                                'status' => $taskData['action']
                                            ])->save();
                                        }

                                        $parentTask->setData([
                                            'product_id' => $parentId,
                                            'task_type' => ProductTaskQueue::TYPE_PRODUCT,
                                            'action' => $taskData['action']
                                        ])->startTask();
                                    }
                                }

                                if ($parentCreation) {
                                    $this->createFeeds[] = [
                                        'type' => ProductTaskQueue::TYPE_PRODUCT,
                                        'product' => $parentId,
                                        'shop' => $productModel->getMadktingStoreId(),
                                        'taskId' => $parentTask->getId()
                                    ];

                                    $this->queueVariationsByProduct($parentId, $taskData['action']);
                                } else {
                                    $this->createFeeds[] = [
                                        'type' => ProductTaskQueue::TYPE_VARIATION,
                                        'product' => $taskData['product_id'],
                                        'shop' => $productModel->getMadktingStoreId(),
                                        'taskId' => $taskData['task_id']
                                    ];
                                }
                                break;

                            /* Variation update action */
                            case ProductTaskQueue::ACTION_UPDATE:

                                $this->updateFeeds[] = [
                                    'type' => ProductTaskQueue::TYPE_VARIATION,
                                    'product' => $taskData['product_id'],
                                    'productPk' => $productModel->getMadktingProductId(),
                                    'shop' => $productModel->getMadktingStoreId(),
                                    'taskId' => $taskData['task_id'],
                                    'parentPk' => $productModel->getMadktingParentId()
                                ];

                                /* Check parent update */
                                $parentId = $this->madktingProductHelper->getParentId($taskData['product_id']);

                                /** @var ProductTaskQueue $parentTask */
                                $parentTask = $this->taskQueueFactory->create()->getCollection()
                                    ->addFieldToFilter('product_id', $parentId)
                                    ->addFieldToFilter('status', ProductTaskQueue::STATUS_WAITING)
                                    ->addFieldToFilter('action', $taskData['action'])
                                    ->setPageSize(1)
                                    ->getFirstItem();

                                if (!empty($parentTask->getId())) {
                                    $parentTask->startTask();

                                    $parentModel = $this->madktingProductFactory->create()->load($parentId, 'magento_product_id');
                                    if (!empty($parentModel->getMadktingProductId())) {
                                        $this->updateFeeds[] = [
                                            'type' => ProductTaskQueue::TYPE_PRODUCT,
                                            'product' => $parentId,
                                            'productPk' => $parentModel->getMadktingProductId(),
                                            'shop' => $productModel->getMadktingStoreId(),
                                            'taskId' => $parentTask->getId()
                                        ];

                                        $this->queueVariationsByProduct($parentId, $taskData['action'], true);
                                    }
                                }
                                break;

                            /* Variation delete action */
                            case ProductTaskQueue::ACTION_DELETE:

                                /* Check parent delete */
                                $parentId = $this->madktingProductHelper->getParentId($taskData['product_id']);

                                if (!empty($parentId)) {
                                    /** @var ProductTaskQueue $parentTask */
                                    $parentTask = $this->taskQueueFactory->create()->getCollection()
                                        ->addFieldToFilter('product_id', $parentId)
                                        ->addFieldToFilter('status', ProductTaskQueue::STATUS_WAITING)
                                        ->addFieldToFilter('action', $taskData['action'])
                                        ->setPageSize(1)
                                        ->getFirstItem();

                                    $parentModel = $this->madktingProductFactory->create()->load($parentId, 'magento_product_id');
                                    $parentMadktingId = $parentModel->getMadktingProductId();
                                }

                                if (!empty($parentTask) && !empty($parentTask->getId())) {
                                    $parentTask->startTask();

                                    if (!empty($parentMadktingId)) {
                                        $this->deleteFeeds[] = [
                                            'type' => ProductTaskQueue::TYPE_PRODUCT,
                                            'product' => $parentId,
                                            'productPk' => $parentMadktingId,
                                            'shop' => $parentModel->getMadktingStoreId(),
                                            'taskId' => $parentTask->getId()
                                        ];

                                        $this->queueVariationsByProduct($parentId, $taskData['action']);
                                    }
                                } else {
                                    $madktingId = $productModel->getMadktingProductId();

                                    if (!empty($parentMadktingId) && !empty($madktingId)) {
                                        $this->deleteFeeds[] = [
                                            'type' => ProductTaskQueue::TYPE_VARIATION,
                                            'product' => $taskData['product_id'],
                                            'productPk' => $madktingId,
                                            'shop' => $productModel->getMadktingStoreId(),
                                            'taskId' => $taskData['task_id'],
                                            'parentPk' => $parentMadktingId
                                        ];
                                    }
                                }
                                break;
                        }
                        break;

                    /* Image task type */
                    case ProductTaskQueue::TYPE_IMAGE:

                        /* Task action */
                        switch ($taskData['action']) {

                            /* Image create action */
                            case ProductTaskQueue::ACTION_CREATE:

                                $this->createFeeds[] = [
                                    'type' => ProductTaskQueue::TYPE_IMAGE,
                                    'product' => $taskData['product_id'],
                                    'shop' => $productModel->getMadktingStoreId(),
                                    'taskId' => $taskData['task_id']
                                ];
                                break;

                            /* Image update action */
                            case ProductTaskQueue::ACTION_UPDATE:

                                $this->updateFeeds[] = [
                                    'type' => ProductTaskQueue::TYPE_IMAGE,
                                    'product' => $taskData['product_id'],
                                    'shop' => $productModel->getMadktingStoreId(),
                                    'taskId' => $taskData['task_id']
                                ];
                                break;

                            /* Image delete action */
                            case ProductTaskQueue::ACTION_DELETE:

                                $this->deleteFeeds[] = [
                                    'type' => ProductTaskQueue::TYPE_IMAGE,
                                    'product' => $taskData['product_id'],
                                    'shop' => $productModel->getMadktingStoreId(),
                                    'taskId' => $taskData['task_id']
                                ];
                                break;
                        }
                        break;
                }

                ++$taskCount;
            }
        }

        /* Create feeds */
        if (!empty($this->createFeeds)) {
            $this->createFeed->execute($this->createFeeds, ProductTaskQueue::ACTION_CREATE);
        }

        /* Update feeds */
        if (!empty($this->updateFeeds)) {
            $this->createFeed->execute($this->updateFeeds, ProductTaskQueue::ACTION_UPDATE);
        }

        /* Delete feeds */
        if (!empty($this->deleteFeeds)) {
            $this->createFeed->execute($this->deleteFeeds, ProductTaskQueue::ACTION_DELETE);
        }

        return $taskCount;
    }

    /**
     * @return bool
     */
    protected function areTaskToDo()
    {
        return $this->taskQueueFactory->create()->getCollection()
            ->addFieldToFilter('status', ProductTaskQueue::STATUS_WAITING)
            ->getSize();
    }

    /**
     * @param int $productId
     * @param int $action
     * @param bool $processVariations
     */
    protected function queueVariationsByProduct($productId, $action, $processVariations = false)
    {
        $variationsId = $this->madktingProductHelper->getVariationsId($productId);
        foreach ($variationsId as $id) {

            /** @var ProductTaskQueue $variationTask */
            $variationTask = $this->taskQueueFactory->create()->getCollection()
                ->addFieldToFilter('product_id', $id)
                ->addFieldToFilter('status', ['nin' => [ProductTaskQueue::STATUS_COMPLETE, ProductTaskQueue::STATUS_ERROR]])
                ->addFieldToFilter('action', $action)
                ->setPageSize(1)
                ->getFirstItem();
            if (!empty($variationTask->getId()) && $variationTask->getStatus() == ProductTaskQueue::STATUS_WAITING) {
                $variationTask->startTask();
            } elseif (empty($variationTask->getId())) {

                /** @var Product $productModel */
                $productModel = $this->madktingProductFactory->create()->load($id, 'magento_product_id');

                if (empty($productModel->getId())) {
                    $productModel = $this->madktingProductFactory->create()->load($productId, 'magento_product_id');

                    $productModel->addData([
                        'product_id' => null,
                        'magento_product_id' => $id,
                        'madkting_product_id' => null,
                        'madkting_parent_id' => null,
                        'madkting_type' => Product::TYPE_VARIATION,
                        'has_variations' => false,
                        'madkting_attributes' => null,
                        'created_at' => null,
                        'updated_at' => null
                    ])->save();
                }

                $variationTask->setData([
                    'product_id' => $id,
                    'task_type' => ProductTaskQueue::TYPE_VARIATION,
                    'status' => ProductTaskQueue::STATUS_PROCESSING,
                    'action' => $action
                ])->startTask();
            }

            if ($processVariations) {

                if (isset($productModel)) {
                    $variationModel = $productModel;
                } else {
                    $variationModel = $this->madktingProductFactory->create()->load($id, 'magento_product_id');
                }

                /* Feed data */
                $feedData = [
                    'type' => ProductTaskQueue::TYPE_VARIATION,
                    'product' => $id,
                    'shop' => $variationModel->getMadktingStoreId(),
                    'taskId' => $variationTask->getId(),
                ];

                switch ($action) {
                    case ProductTaskQueue::ACTION_CREATE:
                        $this->createFeeds[] = $feedData;
                        break;
                    case ProductTaskQueue::ACTION_UPDATE:
                        $feedData['productPk'] = $variationModel->getMadktingProductId();
                        $feedData['parentPk'] = $variationModel->getMadktingParentId();
                        $this->updateFeeds[] = $feedData;
                        break;
                    case ProductTaskQueue::ACTION_DELETE:
                        $feedData['productPk'] = $variationModel->getMadktingProductId();
                        $feedData['parentPk'] = $variationModel->getMadktingParentId();
                        $this->deleteFeeds[] = $feedData;
                        break;
                }
            }
        }
    }
}
