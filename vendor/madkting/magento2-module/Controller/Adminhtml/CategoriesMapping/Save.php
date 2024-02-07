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
use Magento\Backend\App\Action;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
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

    public function __construct(
        CategoriesMapping $categoriesMapping,
        Collection $categoriesMappingCollection,
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

        $magentoCategories = $request['magento_categories'];
        $madktingCategory = $request['madkting_category_id'];
        $mageCatsMapped = $this->categoriesMappingCollection->getColumnValues('magento_category_id');

        /**
         * Compare the parameters array vs. the categories matched, the difference is what we want to save.
         */
        $categoriesToSave = array_diff($magentoCategories, $mageCatsMapped);
        $categoriesNotToSave = array_diff($magentoCategories, $categoriesToSave);

        if(empty($categoriesToSave)){

            $this->messageManager->addErrorMessage(
                __("All the selected categories are already matched. Please try again.")
            );

            return $this->resultRedirectFactory
                        ->create()
                        ->setUrl( $this->_redirect->getRefererUrl() );
        }

       try{
            $this->categoriesMapping;

            foreach ($categoriesToSave as $category){
                $this->categoriesMapping->setData([
                    'madkting_category_id' => $madktingCategory,
                    'magento_category_id' => $category
                ])->save();
            }

           $this->messageManager->addSuccessMessage(
               count($categoriesToSave) > 1 ?
               __('%1 Categories successfully matched.', count($categoriesToSave)) :
               __('Category successfully matched.')
           );

           if(count($categoriesNotToSave)){
               $this->printCategoriesNotMapped($categoriesNotToSave);
           }

       } catch (LocalizedException $e) {

           $this->messageManager->addExceptionMessage(
               $e, $e->getMessage()
           );

           return $this->resultRedirectFactory
               ->create()
               ->setUrl( $this->_redirect->getRefererUrl() );
       } catch (\Exception $e) {

           $this->messageManager->addExceptionMessage(
               $e, $e->getMessage()
           );

           return $this->resultRedirectFactory
               ->create()
               ->setUrl( $this->_redirect->getRefererUrl() );
       }

       $backPath = !empty($this->getRequest()->getParam('back')) ? $this->_redirect->getRefererUrl() : "*/mapping/categories";
       return $this->resultRedirectFactory
                   ->create()
                   ->setPath($backPath);
    }

    /**
     * @param array $categories
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
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Madkting_Connect::categories');
    }
}