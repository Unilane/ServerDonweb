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

namespace Madkting\Connect\Controller\Adminhtml\AttributeOptionMapping;

use Madkting\Connect\Model\AttributeOptionMappingFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Save
 * @package Madkting\Connect\Controller\Adminhtml\AttributeOptionMapping
 */
class Save extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var AttributeOptionMappingFactory
     */
    protected $attributeOptionMappingFactory;

    /**
     * Save constructor
     *
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param AttributeOptionMappingFactory $attributeOptionMappingFactory
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        AttributeOptionMappingFactory $attributeOptionMappingFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->attributeOptionMappingFactory = $attributeOptionMappingFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $response = [
            'error' => true,
            'title' => __('Failed Petition'),
            'message' => ''
        ];

        if ($this->getRequest()->isAjax()) {
            $params = $this->getRequest()->getParams();

            /* Save attribute match */
            try {
                foreach ($params as $id => $values) {
                    if (preg_match('/option(\d|\w)+/', $id)) {

                        $attributeOptionMapping = $this->attributeOptionMappingFactory->create();

                        /* Check Madkting option value */
                        if (!empty($values['madktingId'])) {

                            /* Get mapping id if exists */
                            $mappingId = $attributeOptionMapping
                                ->getCollection()
                                ->addFieldToFilter('madkting_attribute_option_id', $values['madktingId'])
                                ->addFieldToFilter('magento_attribute_option_id', $values['magentoId'])
                                ->setPageSize(1)
                                ->getFirstItem()
                                ->getAttributeOptionMappingId();

                            /* Save registry */
                            $data = [
                                'attribute_option_mapping_id' => $mappingId,
                                'madkting_attribute_option_id' => $values['madktingId'],
                                'magento_attribute_option_id' => $values['magentoId']
                            ];

                            $attributeOptionMapping->setData($data)->save();
                        } else {

                            /* Delete registry if exists */
                            $optionCollection = $attributeOptionMapping
                                ->getCollection()
                                ->addFieldToFilter('magento_attribute_option_id', $values['magentoId']);

                            foreach ($optionCollection as $option) {
                                $option->delete();
                            }
                        }
                    }
                }

                $response['error'] = false;
                $response['title'] = __('Success Petition');
                $response['message'] = __('Attribute option match saved successfully');
            } catch (\Exception $e) {
                $response['message'] = $e->getMessage();
            }
        } else {
            $this->messageManager->addErrorMessage(__('Incorrect petition'));
            return $this->resultRedirectFactory->create()->setPath('admin');
        }

        $json = $this->jsonFactory->create();

        return $json->setData($response);
    }

    /*
	 * Check permission via ACL resource
	 */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Madkting_Connect::attribute_options');
    }
}
