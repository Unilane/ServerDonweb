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

namespace Madkting\Connect\Controller\Adminhtml\AttributeMapping;

use Madkting\Connect\Model\AttributeMappingFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Delete
 * @package Madkting\Connect\Controller\Adminhtml\AttributeMapping
 */
class Delete extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Madkting\Connect\Model\AttributeMapping
     */
    protected $attributeMapping;

    /**
     * Delete constructor.
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param AttributeMappingFactory $attributeMappingFactory
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        AttributeMappingFactory $attributeMappingFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->attributeMapping = $attributeMappingFactory->create();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $response = [
            'error' => true,
            'message' => ''
        ];

        if ($this->getRequest()->isAjax()) {
            $params = $this->getRequest()->getParams();
            if (!empty($params['attributeSetId']) && !empty($params['attributeId'])) {

                try {
                    /* Delete attribute mapping */
                    $attributeCollection = $this->attributeMapping
                        ->getCollection()
                        ->addFieldToFilter('attribute_set_id', $params['attributeSetId'])
                        ->addFieldToFilter('madkting_attribute_id', $params['attributeId']);

                    foreach ($attributeCollection as $attribute) {
                        $attribute->delete();
                    }

                    $response['error'] = false;
                } catch (\Exception $e) {
                    $response['message'] = $e->getMessage();
                }
            } else {
                $response['message'] = __('Required params missing');
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
        return $this->_authorization->isAllowed('Madkting_Connect::attributes');
    }
}
