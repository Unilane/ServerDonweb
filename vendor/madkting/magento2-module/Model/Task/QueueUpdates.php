<?php
/**
 * Created by PhpStorm.
 * User: guillermo
 * Date: 17/01/19
 * Time: 11:43
 */

namespace Madkting\Connect\Model\Task;

use Madkting\Connect\Logger\MadktingLoggerFactory;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Madkting\Connect\Model\ResourceModel\Product\CollectionFactory as ProductsCollection;
use Magento\Framework\Exception\InputException;

/**
 * Class QueueUpdates
 * @package Madkting\Connect\Model\Task
 */
class QueueUpdates
{
    /**
     * @var ProductsCollection
     */
    protected $collectionFactory;

    /**
     * @var ProductTaskQueueFactory
     */
    protected $productTaskQueueFactory;

    /**
     * @var \Madkting\Connect\Logger\MadktingLogger
     */
    protected $madktingLogger;

    /**
     * QueueUpdates constructor
     *
     * @param ProductsCollection $collectionFactory
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     * @param MadktingLoggerFactory $madktingLoggerFactory
     */
    public function __construct(
        ProductsCollection $collectionFactory,
        ProductTaskQueueFactory $productTaskQueueFactory,
        MadktingLoggerFactory $madktingLoggerFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->productTaskQueueFactory = $productTaskQueueFactory;
        $this->madktingLogger = $madktingLoggerFactory->create();
    }

    /**
     * Add products to tasks queue
     *
     * @param null|array $attributes
     * @return array
     */
    public function execute($attributes = null)
    {
        /* Created tasks success/error count */
        $tasksSuccess = 0;
        $tasksError = 0;

        /** @var \Madkting\Connect\Model\Product[] $products */
        $products = $this->collectionFactory->create()->addFieldToFilter('madkting_product_id', array('notnull' => true));
        foreach ($products as $product) {
            try {
                try {
                    $this->productTaskQueueFactory->create()->addData([
                        'product_id' => $product->getMagentoProductId(),
                        'task_type' => $product->getMadktingType(),
                        'status' => ProductTaskQueue::STATUS_WAITING,
                        'action' => ProductTaskQueue::ACTION_UPDATE,
                        'before_action' => ProductTaskQueue::ACTION_NONE,
                        'after_action' => ProductTaskQueue::ACTION_NONE,
                        'selective_sync' => !empty($attributes) ? json_encode($attributes) : null
                    ])->save();

                    $product->setStatus(Product::STATUS_UPDATING)->save();
                    ++$tasksSuccess;
                } catch (InputException $inputException) {
                    continue;
                } catch (\Exception $e) {
                    $product->setStatus(Product::STATUS_SYSTEM_ERROR)->save();
                    ++$tasksError;
                } catch (\Throwable $t) {
                    $product->setStatus(Product::STATUS_SYSTEM_ERROR)->save();
                    ++$tasksError;
                }
            } catch (\Exception $e) {
                ++$tasksError;
                $this->madktingLogger->exception($e, __('There has been an error adding products to update: %1', $e->getMessage()));
            } catch (\Throwable $t) {
                ++$tasksError;
                $this->madktingLogger->exception($t, __('There has been an error adding products to update: %1', $t->getMessage()));
            }
        }

        return [
            'success' => $tasksSuccess,
            'error' => $tasksError
        ];
    }
}
