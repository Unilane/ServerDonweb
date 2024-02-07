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

namespace Madkting\Connect\Block\Adminhtml\CategoriesMapping;


use Madkting\Connect\Block\Adminhtml\MadktingGrid;
use Madkting\Connect\Model\CategoriesMapping\MadktingCategoriesOptions;
use Madkting\Connect\Model\ResourceModel\CategoriesMapping\CollectionFactory;
use Magento\Backend\Block\Widget\Context;
use Magento\Catalog\Model\CategoryFactory;

/**
 * Class Grid
 * @package Madkting\Connect\Block\Adminhtml\CategoriesMapping
 */
class Grid extends MadktingGrid
{
    /**
     * @var string
     */
    protected $gridName = 'madkting-category';
    /**
     * @var CollectionFactory
     */
    private $catMapCollection;
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var MadktingCategoriesOptions
     */
    private $madktingCategoriesOptions;

    public function __construct(
        Context $context,
        CollectionFactory $catMapCollection,
        CategoryFactory $categoryFactory,
        MadktingCategoriesOptions $madktingCategoriesOptions,
        array $data = []
    ){
        parent::__construct($context, $data);
        $this->catMapCollection = $catMapCollection;
        $this->categoryFactory = $categoryFactory;
        $this->madktingCategoriesOptions = $madktingCategoriesOptions;
        $this->addButtons();
        $this->setColumns();
        $this->setRows();
    }

    protected function addButtons()
    {
        $this->addButton(
            'new-category-mapping',
            [
                'label' => __('New Category Match'),
                'class' => 'primary',
                'onclick' => 'setLocation(\'' . $this->getUrl('madkting/categoriesmapping/create') . '\')',
            ]
        );
    }

    protected function setColumns()
    {
        $this->addColumn('madkting_category_name', __('Yuju category'));
        $this->addColumn('magento_categories', __('Magento categories'));
        $this->addColumn('actions', __('Actions'));
    }

    protected function setRows()
    {
        $madktingIds = array_unique(
            $this->catMapCollection->create()->getColumnValues('madkting_category_id')
        );

        $madktingCategories = $this->madktingCategoriesOptions->getSearchableCategoriesArray();
        $this->madktingCategoriesOptions->setCategories($madktingCategories);

        foreach ($madktingIds as $id){

            $madktingCategoryName = $this->madktingCategoriesOptions->getParentsName($madktingCategories[$id]['id_parent']) . $madktingCategories[$id]['name'];

            $actions = "<div class=\"action-select-wrap\"><button class=\"action-select\">" .__('Select')."</button></div>";
            $actions .= "<ul class='action-menu'>";
            $actions .= "<li><a class=\"action-menu-item action-edit\" href=\"". $this->getUrl('madkting/categoriesmapping/edit/') ."id/$id\">".__('Edit')."</a></li>";
            $actions .= "<li><a class=\"action-menu-item action-delete\" data-category-id='$id' data-category-name='$madktingCategoryName' href=\"#\">".__('Delete')."</a></li>";
            $actions .= "</ul>";

            $this->addRow([
                [
                    'columnCode' => 'madkting_category_name',
                    'value' => $madktingCategoryName
                ],
                [
                    'columnCode' => 'magento_categories',
                    'value' => $this->getMagentoCategoryNames($id)
                ],
                [
                    'columnCode' => 'actions',
                    'value' => $actions,
                    'class' => 'data-grid-actions-cell'
                ]
            ]);
        }

    }

    /**
     * @param $madktingCategoryId
     * @return string
     */
    protected function getMagentoCategoryNames($madktingCategoryId)
    {
        $formattedNames = '';

        $mageIds = $this->catMapCollection->create()
                                          ->addFieldToSelect('magento_category_id')
                                          ->addFieldToFilter('madkting_category_id',[
                                              'eq' => $madktingCategoryId
                                          ])->getData();

        foreach ($mageIds as $id) {
            $formattedNames .= "<span class='admin__action-multiselect-crumb'>";
            $formattedNames .= $this->categoryFactory->create()->load($id)->getName();
            $formattedNames .= "</span>";
        }

        return $formattedNames;
    }

}