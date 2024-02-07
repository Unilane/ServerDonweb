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


use Madkting\Connect\Model\CategoriesMappingFactory;
use Madkting\Connect\Model\ResourceModel\CategoriesMapping\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;

class Delete extends Action
{

    /**
     * @var \Madkting\Connect\Model\CategoriesMapping
     */
    private $categoriesMapping;
    /**
     * @var \Madkting\Connect\Model\ResourceModel\CategoriesMapping\Collection
     */
    private $catMapCollectionFactory;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param CategoriesMappingFactory $categoriesMapping
     * @param CollectionFactory $catMapCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        CategoriesMappingFactory $categoriesMapping,
        CollectionFactory $catMapCollectionFactory
    ){
        parent::__construct($context);
        $this->categoriesMapping = $categoriesMapping;
        $this->catMapCollectionFactory = $catMapCollectionFactory->create();
        $this->resultRedirectFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $madCatId = $this->getRequest()->getParam('id');

        if(
            !$this->categoriesMapping->create()
                                     ->load($madCatId, 'madkting_category_id')
                                     ->getData()
        ){
            $this->messageManager->addErrorMessage(
                __('Invalid request')
            );

            return $this->resultRedirectFactory
                        ->create()
                        ->setPath( "*/mapping/categories" );
        }

        $collection = $this->catMapCollectionFactory->addFieldToSelect('category_mapping_id')
                                                    ->addFieldToFilter(
                                                        'madkting_category_id',
                                                        ['eq' => $madCatId]);
        try{

            foreach ($collection as $mapping){
                $mapping->delete();
            }

            $this->messageManager->addSuccessMessage(
                __('The selected match has been successfully removed')
            );

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        }

        return $this->resultRedirectFactory
            ->create()
            ->setPath("*/mapping/categories");
    }
}