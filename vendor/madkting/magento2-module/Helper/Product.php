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

use Madkting\Connect\Logger\MadktingLoggerFactory;
use Madkting\Connect\Model\Config as MadktingConfig;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\SalableStockFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\ConfigurableFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\InputException;
use Magento\InventoryApi\Api\GetSourceItemsBySkuInterface;

/**
 * Class Product
 * @package Madkting\Connect\Helper
 */
class Product extends AbstractHelper
{
    /**
     * Magento Product ID
     *
     * @var int
     */
    protected $productId;

    /**
     * @var ConfigurableFactory
     */
    protected $configurableProductFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurableModel;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var mixed
     */
    protected $salableStock;

    /**
     * @var MadktingConfig
     */
    protected $madktingConfig;

    /**
     * @var \Madkting\Connect\Logger\MadktingLogger
     */
    protected $madktingLogger;
    
    /**
     * @var \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface
     */
    protected $getSourceItemsBySku;


    /**
     * Product constructor
     *
     * @param Context $context
     * @param ConfigurableFactory $configurableProductFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param SalableStockFactory $salableStockFactory
     * @param MadktingConfig $madktingConfig
     * @param MadktingLoggerFactory $madktingLoggerFactory
     */
    public function __construct(
        Context $context,
        ConfigurableFactory $configurableProductFactory,
        CategoryRepositoryInterface $categoryRepository,
        SalableStockFactory $salableStockFactory,
        MadktingConfig $madktingConfig,
        MadktingLoggerFactory $madktingLoggerFactory,
        GetSourceItemsBySkuInterface $getSourceItemsBySku 
    ) {
        parent::__construct($context);
        $this->configurableProductFactory = $configurableProductFactory;
        $this->categoryRepository = $categoryRepository;
        $this->salableStock = $salableStockFactory->create();
        $this->madktingConfig = $madktingConfig;
        $this->madktingLogger = $madktingLoggerFactory->create();
        $this->getSourceItemsBySku = $getSourceItemsBySku;
    }

    /**
     * @param int|null $productId
     * @return $this
     * @throws InputException
     */
    public function setProductId($productId = null)
    {
        if (!empty($productId)) {
            $this->productId = $productId;
        }

        if (empty($this->productId)) {
            throw new InputException(__('No product ID information'));
        }

        return $this;
    }

    /**
     * @param int|null $productId
     * @return int
     * @throws InputException
     */
    public function getProductType($productId = null)
    {
        return !empty($this->getParentId($productId)) ? ProductTaskQueue::TYPE_VARIATION : ProductTaskQueue::TYPE_PRODUCT;
    }

    /**
     * @param int|null $productId
     * @return int|false
     * @throws InputException
     */
    public function getParentId($productId = null)
    {
        $this->setProductId($productId);

        $parentId = $this->getConfigurableModel()->getParentIdsByChild($this->productId);

        return !empty($parentId) ? $parentId[0] : false;
    }

    /**
     * @param int|null $productId
     * @return bool
     * @throws InputException
     */
    public function hasVariations($productId = null)
    {
        return !empty($this->getVariationsId($productId)) ? true : false;
    }

    /**
     * @param int|null $productId
     * @return array
     * @throws InputException
     */
    public function getVariationsId($productId = null)
    {
        $this->setProductId($productId);

        return $this->getConfigurableModel()->getChildrenIds($this->productId)[0];
    }

    /**
     * Get instance of configurable model
     *
     * @return \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected function getConfigurableModel()
    {
        if (empty($this->configurableModel)) {
            $this->configurableModel = $this->configurableProductFactory->create();
        }

        return $this->configurableModel;
    }

    /**
     * Get categories path as just one string
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getCategoriesPath($product)
    {
        $categoryNames = [];
        $categoryPaths = [];
        $childrenDelimiter = ' > ';
        $parentsDelimiter = ' | ';

        $productCategories = $product->getCategoryCollection();
        foreach ($productCategories as $category) {
            if (empty($category->getChildrenCount())) {

                /* Add category to $categoryPaths */
                $lastCatId = $category->getEntityId();
                $categoryPaths[$lastCatId] = '';

                $catPathArray = explode('/', $category->getPath());
                foreach ($catPathArray as $catId) {
                    if (empty($categoryNames[$catId])) {
                        $categoryNames[$catId] = $this->categoryRepository->get($catId)->getName();
                    }

                    if (!empty($categoryNames[$catId])) {
                        $categoryPaths[$lastCatId] .= $categoryNames[$catId] . $childrenDelimiter;
                    }
                }

                /* Clear last children delimiter */
                $categoryPaths[$lastCatId] = rtrim($categoryPaths[$lastCatId], $childrenDelimiter);
            }
        }

        /* Combine categories path in just one string */
        $categoriesPath = '';
        foreach ($categoryPaths as $childPath) {
            $categoriesPath .= $childPath . $parentsDelimiter;
        }

        /* Clear last children delimiter */
        $categoriesPath = rtrim($categoriesPath, $parentsDelimiter);

        return $categoriesPath;
    }

    /**
     * Get available product's stock
     *
     * @param string $sku
     * @return integer
     */
    public function getProductStock($sku)
    {
        $qty = 0;
        try {
            $sourceItems = $this->getSourceItemsBySku->execute($sku);

            $magentoVersion = $this->madktingConfig->getMagentoVersion();
            if (version_compare($magentoVersion, '2.3', '<') || empty($sourceItems)) {
                $stock = $this->salableStock->getStockItemBySku($sku);
                if (!empty($stock->getManageStock())) {
                    $qty = $stock->getQty();
                } else {
                    $qty = $this->madktingConfig->getNoManagedStock();
                }
            } else {
                $stocks = $this->madktingConfig->getSelectedStocks();
                $productStocks = $this->salableStock->execute($sku);
                foreach ($productStocks as $stock) {
                    if (empty($stocks) || in_array($stock['stock_name'], $stocks)) {
                        if (!empty($stock['manage_stock'])) {
                            $qty += !empty($stock['qty']) ? $stock['qty'] : 0;
                        } else {
                            $qty += $this->madktingConfig->getNoManagedStock();
                        }
                    }
                }
            }
            if ($qty < 0) {
                $qty = 0;
            }
        } catch (\Exception $e) {
            $this->madktingLogger->exception($e, __('There has been an error getting product stock: %1', $e->getMessage()));
        } catch (\Throwable $t) {
            $this->madktingLogger->exception($t, __('There has been an error getting product stock: %1', $t->getMessage()));
        }
        return $qty;
    }
}
