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
use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\Product;
use Madkting\Connect\Model\ProductFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassCreate
 * @package Madkting\Connect\Controller\Adminhtml\Product
 */
class MassCreate extends Action
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
     * @var Config
     */
    protected $madktingConfig;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductTaskQueueFactory
     */
    protected $queueFactory;

    /**
     * @var \Madkting\Connect\Helper\Product
     */
    protected $madktingProductHelper;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * MassCreate constructor
     *
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $catalogCollectionFactory
     * @param Config $madktingConfig
     * @param ProductFactory $productFactory
     * @param ProductTaskQueueFactory $queueFactory
     * @param ProductHelper $madktingProductHelper
     * @param JsonFactory $jsonFactory
     * @param MadktingLogger $logger
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $catalogCollectionFactory,
        Config $madktingConfig,
        ProductFactory $productFactory,
        ProductTaskQueueFactory $queueFactory,
        ProductHelper $madktingProductHelper,
        JsonFactory $jsonFactory,
        MadktingLogger $logger
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->catalogCollection = $catalogCollectionFactory->create();
        $this->madktingConfig = $madktingConfig;
        $this->productFactory = $productFactory;
        $this->queueFactory = $queueFactory;
        $this->madktingProductHelper = $madktingProductHelper;
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $response = [
            'error' => true,
            'message' => ''
        ];

        if (!empty($madktingShop = $this->getRequest()->getParam('madktingShop'))) {

            /* Get products selected */
            $productsIds = [];
            try {
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
                $productCollection = $this->filter->getCollection($this->catalogCollection);

                if (!$this->madktingConfig->isUploadDisabledProductsEnabled() && empty($this->getRequest()->getParam('uploadDisabled'))) {
                    $productCollection->addFieldToFilter('status', Status::STATUS_ENABLED);
                }

                $productsIds = $productCollection->getAllIds();
            } catch (LocalizedException $e) {
                $response['message'] = __('Invalid products information');
            }

            if (!empty($productsIds)) {

                try {
                    /** @var \Madkting\Connect\Model\Product $madktingProduct */
                    $madktingProduct = $this->productFactory->create();

                    $productData = [
                        'magento_store_id' => $this->madktingConfig->getSelectedStore(),
                        'madkting_store_id' => $madktingShop,
                        'status' => Product::STATUS_CREATING
                    ];

                    /** @var ProductTaskQueue $taskQueue */
                    $taskQueue = $this->queueFactory->create();

                    $taskData = [
                        'status' => ProductTaskQueue::STATUS_WAITING,
                        'action' => ProductTaskQueue::ACTION_CREATE
                    ];

                    $count = 0;
                    foreach ($productsIds as $productId) {
                        $exists = $madktingProduct->unsetData()->load($productId, 'magento_product_id');
                        if (empty($exists->getId())) {
                            $productHelper = $this->madktingProductHelper->setProductId($productId);
                            $productType = $productHelper->getProductType();
                            $hasVariations = $productHelper->hasVariations();

                            $productData['magento_product_id'] = $productId;
                            $productData['madkting_type'] = $productType;
                            $productData['has_variations'] = $hasVariations;
                            $madktingProduct->setData($productData)->save();

                            try {
                                $taskData['product_id'] = $productId;
                                $taskData['task_type'] = $productType;
                                $taskQueue->setData($taskData)->save();
                                ++$count;
                            } catch (InputException $e) {
                                continue;
                            } catch (\Exception $e) {
                                $madktingProduct->setStatus(Product::STATUS_SYSTEM_ERROR)->save();
                            }
                        } else {
                            if ($exists->getMadktingStoreId() == $madktingShop && empty($exists->getMadktingProductId())) {
                                try {
                                    $taskData['product_id'] = $productId;
                                    $taskData['task_type'] = $this->madktingProductHelper->getProductType($productId);
                                    $taskQueue->setData($taskData)->save();

                                    $exists->setStatus(Product::STATUS_CREATING)
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
                    }

                    $response['error'] = false;
                    if ($count > 0) {
                        $response['message'] = __('Creating %1 %2 in Yuju', $count, $count > 1 ? __('products') : __('product'));
                    } else {
                        $response['message'] = __('There are no new products to create in selection');
                    }
                } catch (\Exception $e) {
                    $logCase = $this->logger->error($response['message'] . $e->getMessage());
                    $response['message'] = __('Something went wrong creating products in Yuju');
                }
            } else {
                $response['error'] = false;
                $response['message'] = __('No products selected');
            }
        } else {
            $response['message'] = __('Required params missing');
        }

        if ($this->getRequest()->isAjax()) {
            if (!$response['error']) {
                $this->messageManager->addSuccessMessage($response['message']);
                $response['location'] = $this->getUrl('catalog/product');
            }

            $json = $this->jsonFactory->create();

            return $json->setData($response);
        } else {
            if ($response['error']) {
                $this->messageManager->addErrorMessage($response['message']);
            } else {
                $this->messageManager->addSuccessMessage($response['message']);
            }
            return $this->resultRedirectFactory->create()->setPath('catalog/product');
        }
    }

    /*
	 * Check permission via ACL resource
	 */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Madkting_Connect::product_create');
    }
}
