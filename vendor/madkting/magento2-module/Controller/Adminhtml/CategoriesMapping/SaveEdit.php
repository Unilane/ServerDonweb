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

namespace Madkting\Connect\Controller\Adminhtml\CategoriesMapping;

use Madkting\Connect\Model\CategoriesMapping;
use Madkting\Connect\Model\ResourceModel\CategoriesMapping\Collection;
use Madkting\Connect\Model\ResourceModel\CategoriesMapping\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\ResponseInterface;

class SaveEdit extends Action
{

    /**
     * @var \Madkting\Connect\Model\CategoriesMapping
     */
    private $categoriesMapping;
    /**
     * @var Collection
     */
    private $categoriesMappingCollection;
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * SaveEdit constructor.
     * @param CategoriesMapping $categoriesMapping
     * @param CollectionFactory $categoriesMappingCollection
     * @param CategoryFactory $categoryFactory
     * @param Action\Context $context
     */
    public function __construct(
        CategoriesMapping $categoriesMapping,
        CollectionFactory $categoriesMappingCollection,
        CategoryFactory $categoryFactory,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->categoriesMapping = $categoriesMapping;
        $this->categoriesMappingCollection = $categoriesMappingCollection;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /**
         * Form key and request validation
         */
        if( !$this->_formKeyValidator->validate($this->getRequest()) ){

            $this->messageManager->addErrorMessage(
                __("Request invalid, please try again.")
            );

            return $this->resultRedirectFactory
                        ->create()
                        ->setPath("*/mapping/categories");
        }

        $request = $this->getRequest()
                        ->getParam('categories_mapping');

        $madktingCategory = $request['madkting_category'];
        $requestedCategories = empty($request['magento_categories'] ) ? array() : $request['magento_categories'];

        $ownedCategories = $this->getOwnedCategories($madktingCategory);
        $allMageCatsMapped = $this->categoriesMappingCollection
                                  ->create()
                                  ->getColumnValues('magento_category_id');

        /**
         * Categories that are already matched with another madkting category.
         */
        $protectedCategories = array_diff($allMageCatsMapped, $ownedCategories);

        /**
         * Compare the requested categories vs. the already matched with the madkting category selected.
         * The difference is the categories the user remove in the multi select in form.
         */
        $categoriesToRemove = array_diff($ownedCategories, $requestedCategories);

        /**
         * Compare the requested categories vs. protected categories and owned categories, the difference
         * is what we want to save.
         */
        $categoriesToSave = array_diff($requestedCategories, $protectedCategories, $ownedCategories);

        $discardedCategories = array_diff($requestedCategories, $categoriesToSave, $ownedCategories);

       try{

            foreach ($categoriesToSave as $category){
                $this->categoriesMapping->setData([
                    'madkting_category_id' => $madktingCategory,
                    'magento_category_id' => $category
                ])->save();
            }

           foreach ($categoriesToRemove as $category){
              $mapping = $this->categoriesMapping->load($category,'magento_category_id');
              $mapping->delete();
           }

           $this->printSuccessNotification(count($categoriesToSave), count($categoriesToRemove));

           if(count($discardedCategories)){
               $this->printCategoriesNotMapped($discardedCategories);
           }
       } catch (\Exception $e) {

           $this->messageManager->addExceptionMessage(
               $e, $e->getMessage()
           );

           return $this->resultRedirectFactory
               ->create()
               ->setUrl( $this->_redirect->getRefererUrl() );
       }

       return $this->resultRedirectFactory
                   ->create()
                   ->setPath( "*/mapping/categories" );
    }

    /**
     * @param $categories
     */
    private function printCategoriesNotMapped($categories)
    {
        $categoryFactory = $this->categoryFactory->create();

        foreach($categories as $category){
            $categoryName =  $categoryFactory->load($category)->getName();
            $this->messageManager->addWarningMessage(
                __('%1 category has not been saved. This category was already matched.', $categoryName)
            );
        }
    }

    /**
     * @param $categoryId
     * @return array
     */
    private function getOwnedCategories($categoryId)
    {
        $formattedArray = [];
        $categories = $this->categoriesMappingCollection
                            ->create()
                            ->addFieldToSelect('magento_category_id')
                            ->addFieldToFilter(
                                'madkting_category_id',
                                    ['eq' => $categoryId])
                            ->getData();

        foreach ($categories as $category){
            $formattedArray[] = $category['magento_category_id'];
        }

        return $formattedArray;
    }

    /**
     * @param $categoriesToSave
     * @param $categoriesToRemove
     */
    private function printSuccessNotification($categoriesToSave, $categoriesToRemove)
    {
        if($categoriesToSave) {
            $this->messageManager->addSuccessMessage(
                $categoriesToSave > 1 ?
                    __('%1 New categories successfully matched.', $categoriesToSave) :
                    __('1 category successfully matched.')
            );
        }

        if($categoriesToRemove) {
            $this->messageManager->addSuccessMessage(
                $categoriesToRemove > 1 ?
                    __('%1 categories successfully disassociated.', $categoriesToRemove) :
                    __('1 category successfully disassociated.')
            );
        }

    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Madkting_Connect::categories');
    }

}