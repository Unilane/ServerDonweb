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

use Madkting\Connect\Helper\Data as MadktingHelper;
use Madkting\Connect\Model\AttributeFactory;
use Madkting\Connect\Model\AttributeMappingFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Save
 * @package Madkting\Connect\Controller\Adminhtml\AttributeMapping
 */
class Save extends Action
{
    /**
     * Fields with empty value allowed
     *
     * @var array
     */
    protected $emptyValueAllowed;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Madkting\Connect\Model\Attribute
     */
    protected $attribute;

    /**
     * @var AttributeMappingFactory
     */
    protected $attributeMappingFactory;

    /**
     * @var \Madkting\Connect\Model\AttributeMapping
     */
    protected $attributeMapping;

    /**
     * Save constructor
     *
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param AttributeFactory $attributeFactory
     * @param AttributeMappingFactory $attributeMappingFactory
     * @param MadktingHelper $madktingHelper
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        AttributeFactory $attributeFactory,
        AttributeMappingFactory $attributeMappingFactory,
        MadktingHelper $madktingHelper
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->attributeFactory = $attributeFactory;
        $this->attributeMappingFactory = $attributeMappingFactory;
        $this->emptyValueAllowed = $madktingHelper->getEmptyValueAllowed();
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
            if (!empty($params['attributeSetId'])) {

                /* Validate data */
                $attributesToSave = [];
                $attributesError = [];
                $rowCount = 0;
                foreach ($params as $id => &$values) {
                    if (preg_match_all('/attribute(-dynamic)?\d+/', $id, $match, PREG_SET_ORDER)) {
                        ++$rowCount;

                        /* Check if row is dynamic */
                        $dynamicRow = false;
                        if ($matchValue = !empty($match[0][1])) {
                            $dynamicRow = $matchValue != '-dynamic' ?: true;
                        }

                        /* Search for errors */
                        if ($dynamicRow
                            && (!isset($values['madktingId']) || $values['madktingId'] === '')
                            && (!isset($values['magentoId']) || $values['magentoId'] === '')
                            && (!isset($values['defaultValue']) || $values['defaultValue'] === '')) {
                            continue;
                        } elseif (!isset($values['madktingId']) || $values['madktingId'] === '') {
                            $attributesError[] = [
                                'rowName' => (string)__('Row %1', $rowCount),
                                'errorMessage' => __('Yuju\'s attribute missing')
                            ];
                            continue;
                        } elseif ((!isset($values['magentoId']) || $values['magentoId'] === '') && (!isset($values['defaultValue']) || $values['defaultValue'] === '')) {
                            if (empty($this->attribute)) {
                                $this->attribute = $this->attributeFactory->create();
                            }

                            /* Get attribute data */
                            $attribute = $this->attribute->load($values['madktingId']);

                            switch ($attribute->getAttributeCode()) {
                                case 'stock':
                                    $values['magentoId'] = NULL;
                                    break;
                                case 'shipping_price':
                                    $values['defaultValue'] = $values['defaultValue'] != '' ? $values['defaultValue'] : '0';
                                    break;
                                default:

                                    /* Fields empty value is not allowed */
                                    if (!in_array($attribute->getAttributeCode(), $this->emptyValueAllowed)) {
                                        $attributesError[] = [
                                            'rowName' => $attribute->getAttributeLabel(),
                                            'errorMessage' => __('Must provide Magento\'s attribute or default value at least')
                                        ];
                                        continue 2;
                                    }
                            }
                        }

                        /* Add data to save */
                        if (empty($this->attributeMapping)) {
                            $this->attributeMapping = $this->attributeMappingFactory->create();
                        }

                        /* Get mapping id if exists */
                        $mappingId = $this->attributeMapping
                            ->getCollection()
                            ->addFieldToFilter('attribute_set_id', $params['attributeSetId'])
                            ->addFieldToFilter('madkting_attribute_id', $values['madktingId'])
                            ->setPageSize(1)
                            ->getFirstItem()
                            ->getAttributeMappingId();

                        $attributesToSave[] = [
                            'attribute_mapping_id' => $mappingId,
                            'attribute_set_id' => $params['attributeSetId'],
                            'magento_attribute_id' => $values['magentoId'] != '' ? $values['magentoId'] : NULL,
                            'madkting_attribute_id' => $values['madktingId'],
                            'default_value' => $values['defaultValue']
                        ];
                    }
                }

                /* Return error if exists */
                if (!empty($attributesError)) {

                    /* Add errors */
                    foreach ($attributesError as $error) {
                        $response['message'] .= '-> <b>' . $error['rowName'] . '</b> - ' . $error['errorMessage'] . '<br />';
                    }
                } elseif (!empty($attributesToSave)) {

                    /* Save attribute match */
                    try {
                        foreach ($attributesToSave as $attribute) {
                            $attributeMapping = $this->attributeMappingFactory->create();
                            $attributeMapping->setData($attribute)->save();
                        }

                        $response['error'] = false;
                        $response['title'] = __('Success Petition');
                        $response['message'] = __('Attribute match saved successfully');
                    } catch (\Exception $e) {
                        $response['message'] = $e->getMessage();
                    }
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
