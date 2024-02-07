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

namespace Madkting\Connect\Controller\Adminhtml\Product;

use Madkting\Connect\Helper\Product as ProductHelper;
use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassUpdate
 * @package Madkting\Connect\Controller\Adminhtml\Product
 */
class MassUpdate extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $catalogCollection;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductTaskQueueFactory
     */
    protected $queueFactory;

    /**
     * @var ProductHelper
     */
    protected $madktingProductHelper;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * MassUpdate constructor
     *
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $catalogCollectionFactory
     * @param ProductFactory $productFactory
     * @param ProductTaskQueueFactory $queueFactory
     * @param ProductHelper $madktingProductHelper
     * @param MadktingLogger $logger
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $catalogCollectionFactory,
        ProductFactory $productFactory,
        ProductTaskQueueFactory $queueFactory,
        ProductHelper $madktingProductHelper,
        MadktingLogger $logger
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->catalogCollection = $catalogCollectionFactory->create();
        $this->productFactory = $productFactory;
        $this->queueFactory = $queueFactory;
        $this->madktingProductHelper = $madktingProductHelper;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $response = [
            'error' => true,
            'message' => ''
        ];

        /* Get products selected */
        $productsIds = [];
        try {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $this->filter->getCollection($this->catalogCollection);

            $productsIds = $productCollection->getAllIds();
        } catch (LocalizedException $e) {
            $response['message'] = __('Invalid products information');
        }

        if (!empty($productsIds)) {
            try {
                /** @var ProductTaskQueue $taskQueue */
                $taskQueue = $this->queueFactory->create();

                $taskData = [
                    'status' => ProductTaskQueue::STATUS_WAITING,
                    'action' => ProductTaskQueue::ACTION_UPDATE
                ];

                $count = 0;
                foreach ($productsIds as $productId) {

                    /* Get product Madkting's id */
                    $madktingProduct = $this->productFactory->create()->load($productId,'magento_product_id');

                    if (!empty($productMadktingId = $madktingProduct->getMadktingProductId())) {
                        try {
                            $taskData['product_id'] = $productId;
                            $taskData['task_type'] = $this->madktingProductHelper->getProductType($productId);
                            $taskQueue->setData($taskData)->save();

                            $madktingProduct->setStatus(Product::STATUS_UPDATING)
                                ->setStatusMessage(null)
                                ->save();

                            ++$count;
                        } catch (InputException $e) {
                            continue;
                        } catch (\Exception $e) {
                            $madktingProduct->setStatus(Product::STATUS_SYSTEM_ERROR)->save();
                        }
                    }
                }

                $response['error'] = false;
                if ($count > 0) {
                    $response['message'] = __('Updating %1 %2 in Yuju', $count, $count > 1 ? __('products') : __('product'));
                } else {
                    $response['message'] = __('There are no new products to update in selection');
                }
            } catch (\Exception $e) {
                $response['message'] = __('Something went wrong updating products in Yuju');
                $this->logger->debug($response['message'] . $e->getMessage());
            }
        } else {
            $response['error'] = false;
            $response['message'] = __('No products selected');
        }

        if ($response['error']) {
            $this->messageManager->addErrorMessage($response['message']);
        } else {
            $this->messageManager->addSuccessMessage($response['message']);
        }
        return $this->resultRedirectFactory->create()->setPath('catalog/product');
    }

    /*
	 * Check permission via ACL resource
	 */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Madkting_Connect::product_update');
    }
}
