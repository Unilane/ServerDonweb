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

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class CreateForm
 * @package Madkting\Connect\Controller\Adminhtml\Product
 */
class CreateForm extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $catalogCollection;

    /**
     * CreateForm constructor
     *
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param Filter $filter
     * @param CollectionFactory $catalogCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        Filter $filter,
        CollectionFactory $catalogCollectionFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->filter = $filter;
        $this->catalogCollection = $catalogCollectionFactory->create();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
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
            $pageResult = $this->pageFactory->create();
            $pageResult->setActiveMenu('Magento_Catalog::catalog_products');
            $pageResult->getConfig()->getTitle()->prepend($this->getTitle($productsIds));

            return $pageResult;
        } else {
            $this->messageManager->addErrorMessage(__('No products selected'));
            return $this->resultRedirectFactory->create()->setPath('catalog/product');
        }
    }

    /**
     * Get form title
     *
     * @param int $products
     * @return string
     */
    protected function getTitle($products)
    {
        if (count($products) > 1) {
            $title = __('Create %1 on Yuju', __('Products'));
        } else {
            $title = __('Create %1 on Yuju', __('Product'));
        }

        return $title;
    }

    /*
	 * Check permission via ACL resource
	 */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Madkting_Connect::product_create');
    }
}
