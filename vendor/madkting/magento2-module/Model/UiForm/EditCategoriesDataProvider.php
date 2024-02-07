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

namespace Madkting\Connect\Model\UiForm;


use Madkting\Connect\Model\CategoriesMappingFactory;
use Madkting\Connect\Model\ResourceModel\CategoriesMapping\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class EditCategoriesDataProvider extends DataProvider
{

    /**
     * @var \Madkting\Connect\Model\ResourceModel\CategoriesMapping\Collection $catMapCollectionFactory
     */
    private $catMapCollectionFactory;
    /**
     * @var array
     */
    protected $loadedData;
    /**
     * @var \Madkting\Connect\Model\CategoriesMapping $categoriesMappingFactory
     */
    private $categoriesMappingFactory;

    public function __construct(
        CollectionFactory $catMapCollectionFactory,
        CategoriesMappingFactory $categoriesMappingFactory,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ){
        $this->catMapCollectionFactory = $catMapCollectionFactory;
        $this->loadedData = array();
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->categoriesMappingFactory = $categoriesMappingFactory;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $madktingCategories = $this->getMadktingMappedCategories();

        foreach ($madktingCategories as $category){
            $this->loadedData[(int)$category]['categories_mapping'] = [
                'madkting_category_id' => $category,
                'madkting_category' => $category,
                'magento_categories' => $this->getMageCategories($category)
            ];
        }

        return $this->loadedData;
    }

    /**
     * @return array
     */
    private function getMadktingMappedCategories()
    {
        return array_unique(
            $this->catMapCollectionFactory
                ->create()
                ->getColumnValues('madkting_category_id')
        );
    }

    /**
     * @param $categoryMadktingId
     * @return mixed
     */
    private function getMageCategories($categoryMadktingId)
    {
        return $this->catMapCollectionFactory
             ->create()
             ->addFieldToFilter(
                 'madkting_category_id',
                 ['eq' => $categoryMadktingId]
             )->getColumnValues('magento_category_id');
    }
}