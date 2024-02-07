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
 * @author Israel Calderón Aguilar <israel@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Controller\Adminhtml\Product;

use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Madkting\Connect\Model\ResourceModel\Product\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\InputException;

/**
 * Class QueueUp
 * @package Madkting\Connect\Controller\Adminhtml\Product
 */
class QueueUp extends Action
{

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * @var MadktingProductFactory
     */
    protected $madktingProductFactory;
    /**
     * @var ProductTaskQueueFactory
     */
    protected $productTaskQueueFactory;
    /**
     * @var CollectionFactory
     */
    protected $collection;
    /**
     * @var Configurable
     */
    protected $configurable;
    /**
     * @var Config
     */
    protected $config;

    /**
     * QueueUp constructor.
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param CollectionFactory $collection
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     * @param Config $config
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        CollectionFactory $collection,
        ProductTaskQueueFactory $productTaskQueueFactory,
        Config $config
    ){
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory->create();
        $this->collection = $collection->create()->addFieldToFilter('madkting_product_id', array('notnull' => true));
        $this->productTaskQueueFactory = $productTaskQueueFactory;
        $this->config = $config;
    }

    /**
     * Dispatch request
     *
     * @return JsonFactory
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {

        $selectedFields = $this->getRequest()->getParam('fields');

        /**
         * Form key and request validation
         */
        if( !$this->_formKeyValidator->validate($this->getRequest()) || empty($selectedFields) ){

            $this->messageManager->addErrorMessage(__('Invalid request'));

            return $this->jsonFactory->setData([
                'success' => false,
                'message' => __('Invalid request')
            ]);
        }

        $products = $this->collection;
        $errorCount = 0;

        foreach($products as $product):
            try {
                $this->productTaskQueueFactory->create()->addData([
                    'product_id' => $product->getMagentoProductId(),
                    'task_type' => $product->getMadktingType(),
                    'status' => ProductTaskQueue::STATUS_WAITING,
                    'action' => ProductTaskQueue::ACTION_UPDATE,
                    'before_action' => ProductTaskQueue::ACTION_NONE,
                    'after_action' => ProductTaskQueue::ACTION_NONE,
                    'selective_sync' => $selectedFields === 'all' ? null : $selectedFields
                ])->save();

                $product->setStatus(Product::STATUS_UPDATING)->save();

            } catch (InputException $inputException) {

            } catch (\Exception $e) {
                $product->setStatus(Product::STATUS_SYSTEM_ERROR)->save();
                ++$errorCount ;
            }
        endforeach;

        if ( ($errorCount === $products->count()) && ($products->count() > 0) ) {
            $response = [
                'success' => false,
                'message' => __('No product could be placed in the queue, please see the debug log for more information')
            ];
            $this->messageManager->addErrorMessage($response['message']);

            return $this->jsonFactory->setData($response);

        } elseif ( $products->count() === 0 ) {
            $response = [
                'success' => false,
                'message' => __("You don't have any product in Yuju yet. You must have products in Yuju in order to run this process.")
            ];
            $this->messageManager->addErrorMessage($response['message']);

            return $this->jsonFactory->setData($response);
        }

        $this->config->setLastSyncDate();

        $response = [
            'success' => true,
            'errors' => $errorCount,
            'message' => $errorCount > 0 ? __('Synchronization process successfully started with %1 errors', $errorCount) :
                                           __('Synchronization process successfully started without errors')
        ];
        $this->messageManager->addSuccessMessage($response['message']);

        return $this->jsonFactory->setData($response);
    }
}