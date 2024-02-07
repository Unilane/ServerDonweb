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

namespace Madkting\Connect\Model\CategoriesMapping;


use Madkting\Connect\Model\ResourceModel\CategoriesMapping\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Ui\Component\Product\Form\Categories\Options;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Category as CategoryModel;

class MagentoCategoriesOptions extends Options
{
    /**
     * @var Collection
     */
    private $categoriesMappingCollection;

    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        RequestInterface $request,
        Collection $categoriesMappingCollection
    ){
        parent::__construct($categoryCollectionFactory, $request);
        $this->categoriesMappingCollection = $categoriesMappingCollection;
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     */
    protected function getCategoriesTree()
    {
        if ($this->categoriesTree === null) {
            $storeId = $this->request->getParam('store');
            /* @var $matchingNamesCollection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $matchingNamesCollection = $this->categoryCollectionFactory->create();

            $matchingNamesCollection->addAttributeToSelect('path')
                ->addAttributeToFilter('entity_id', ['neq' => CategoryModel::TREE_ROOT_ID])
                ->setStoreId($storeId);

            $shownCategoriesIds = [];

            /** @var \Magento\Catalog\Model\Category $category */
            foreach ($matchingNamesCollection as $category) {
                foreach (explode('/', $category->getPath()) as $parentId) {
                    $shownCategoriesIds[$parentId] = 1;
                }
            }

            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $collection = $this->categoryCollectionFactory->create();
            $magentoCategoriesInMapping = $this->categoriesMappingCollection->getColumnValues('magento_category_id');

            $collection->addAttributeToFilter('entity_id', ['in' => array_keys($shownCategoriesIds)])
                ->addAttributeToSelect(['name', 'is_active', 'parent_id'])
                ->setStoreId($storeId);

            $categoryById = [
                CategoryModel::TREE_ROOT_ID => [
                    'value' => CategoryModel::TREE_ROOT_ID
                ],
            ];

            foreach ($collection as $category) {
                foreach ([$category->getId(), $category->getParentId()] as $categoryId) {
                    if (!isset($categoryById[$categoryId])) {
                        $categoryById[$categoryId] = ['value' => $categoryId];
                    }
                }

                $categoryById[$category->getId()]['is_active'] = $category->getIsActive();
                $categoryById[$category->getId()]['label'] = $category->getName();
                $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];

                foreach ($magentoCategoriesInMapping as $categoryKey){
                    if($categoryKey == $category->getId()){
                        $categoryById[$category->getId()]['label'] .= ' - ' . __('Already matched');
                    }
                }

            }

            $this->categoriesTree = $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];
        }

        return $this->categoriesTree;
    }

}