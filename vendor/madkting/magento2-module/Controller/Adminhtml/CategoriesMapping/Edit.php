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
 * @author Carlos Guillermo Jiménez Salcedo <guillermo@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Controller\Adminhtml\CategoriesMapping;


use Madkting\Connect\Model\CategoriesMapping;
use Madkting\Connect\Model\CategoriesMapping\MadktingCategoriesOptions;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    /**
     * @var MadktingCategoriesOptions
     */
    protected $madktingCategoriesOptions;

    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var CategoriesMapping
     */
    private $categoriesMapping;

    public function __construct(
        Action\Context $context,
        CategoriesMapping $categoriesMapping,
        PageFactory $pageFactory,
        MadktingCategoriesOptions $madktingCategoriesOptions
    ) {
        $this->categoriesMapping = $categoriesMapping;
        $this->pageFactory = $pageFactory;
        $this->madktingCategoriesOptions = $madktingCategoriesOptions;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        /**
         * Validate request.
         */
        if ( null == $params || empty($params['id']) ) {

            $this->messageManager->addErrorMessage(
                __("Request invalid, please try again.")
            );

            return $this->resultRedirectFactory
                ->create()
                ->setPath("*/mapping/categories");
        }

        /**
         * Validate madkting category id
         */
        if(!$this->validateMadktingCategory($params['id'])){

            $this->messageManager->addErrorMessage(
                __("The category requested does not have any match")
            );

            return $this->resultRedirectFactory
                ->create()
                ->setPath("*/mapping/categories");
        }

        $resultPage = $this->pageFactory->create();

        $resultPage->getConfig()
                   ->getTitle()
                   ->prepend($this->getTitle());

        $this->_view->loadLayout();
        $this->_view->renderLayout();

        return null;
    }

    /**
     * Validate if the category requested is already matched.
     * @param $categoryId
     * @return bool
     */
    private function validateMadktingCategory($categoryId)
    {
        if(
            null == $this->categoriesMapping
                         ->load($categoryId,'madkting_category_id')
                         ->getData()
        ){
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Madkting_Connect::categories');
    }

    /**
     * Get form title
     */
    protected function getTitle()
    {
        $title = __('Edit Category Match');

        $madktingCategoryId = $this->_request->getParam('id');
        $madktingCategories = $this->madktingCategoriesOptions->getSearchableCategoriesArray();
        $requestedCategory = $madktingCategories[(int)$madktingCategoryId];
        $parentCategory = $this->madktingCategoriesOptions->setCategories($madktingCategories)->getParentsName($requestedCategory['id_parent']);

        if (!empty($requestedCategory)) {
            $title = $requestedCategory['name'];

            if (!empty($parentCategory)) {
                $title = $parentCategory . ' ' . $title;
            }
        }

        return $title;
    }
}