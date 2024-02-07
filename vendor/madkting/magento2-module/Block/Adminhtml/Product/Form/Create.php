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

namespace Madkting\Connect\Block\Adminhtml\Product\Form;

use Madkting\Connect\Helper\Data;
use Madkting\Connect\Model\Config;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Backend\Block\Widget;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class Create
 * @package Madkting\Connect\Block\Adminhtml\Product\Form
 */
class Create extends Widget\Container
{
    /**
     * @var array
     */
    protected $sentProductsIds;

    /**
     * @var array
     */
    protected $selectedProducts;

    /**
     * @var array
     */
    protected $excludedProducts;

    /**
     * @var array
     */
    protected $madktingShops;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Data
     */
    protected $madktingHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $catalogCollection;

    /**
     * Create constructor.
     * @param Widget\Context $context
     * @param Config $config
     * @param Filter $filter
     * @param CollectionFactory $catalogCollectionFactory
     * @param Data $madktingHelper
     * @param array $data
     */
    public function __construct(
        Widget\Context $context,
        Config $config,
        Filter $filter,
        CollectionFactory $catalogCollectionFactory,
        Data $madktingHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->filter = $filter;
        $this->catalogCollection = $catalogCollectionFactory->create();
        $this->madktingHelper = $madktingHelper;

        /* Add grid buttons */
        $this->addButtons();
    }

    /**
     * Add form buttons
     */
    protected function addButtons()
    {
        /* Add buttons */
        $this->addButton(
            'back_button',
            [
                'label' => __('Back'),
                'class' => 'back',
                'onclick' => 'setLocation(\'' . $this->getBackAction() . '\')'
            ]
        );

        $this->addButton(
            'create_products_button',
            [
                'label' => $this->getCreateButtonLabel(),
                'class' => 'primary'
            ]
        );
    }

    /**
     * Back controller
     *
     * @return string
     */
    protected function getBackAction()
    {
        return $this->getUrl('catalog/product/');
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        if (count($this->getSentProductsIds()) > 1) {
            $title = __('Create %1', __('Products'));
        } else {
            $title = __('Create %1', __('Product'));
        }

        return $title;
    }

    /**
     * @return array
     */
    protected function getSentProductsIds()
    {
        if (!isset($this->sentProductsIds)) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
            $productCollection = $this->filter->getCollection($this->catalogCollection);
            $this->sentProductsIds = $productCollection->getAllIds();
        }

        return $this->sentProductsIds;
    }

    /**
     * @return array
     */
    public function getSelectedProducts()
    {
        if (!isset($this->selectedProducts)) {
            $this->selectedProducts = $this->getRequest()->getParam(Filter::SELECTED_PARAM);
        }

        return $this->selectedProducts;
    }

    /**
     * @return array
     */
    public function getExcludedProducts()
    {
        if (!isset($this->excludedProducts)) {
            $this->excludedProducts = $this->getRequest()->getParam(Filter::EXCLUDED_PARAM);
        }

        return $this->excludedProducts;
    }

    /**
     * Get Madkting Shops available for token
     *
     * @return array
     */
    public function getMadktingShops()
    {
        if (!isset($this->madktingShops)) {
            $this->madktingShops = $this->madktingHelper->getMadktingShops();
        }

        return $this->madktingShops;
    }

    /**
     * If upload disabled products option is enabled
     *
     * @return bool
     */
    public function isUploadDisabledProductsEnabled()
    {
        return $this->config->isUploadDisabledProductsEnabled();
    }
}
