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

use Madkting\Connect\Model\AttributeFactory;
use Madkting\Connect\Model\AttributeMappingFactory;
use Madkting\Connect\Model\AttributeOptionFactory;
use Magento\Backend\App\Action;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Collection;

/**
 * Class Newrow
 * @package Madkting\Connect\Controller\Adminhtml\AttributeMapping
 */
class NewRow extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var AttributeOptionFactory
     */
    protected $attributeOptionFactory;

    /**
     * @var AttributeMappingFactory
     */
    protected $attributeMappingFactory;

    /**
     * @var AttributeManagementInterface
     */
    protected $attributeManagement;

    /**
     * NewRow constructor.
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param AttributeFactory $attributeFactory
     * @param AttributeOptionFactory $attributeOptionFactory
     * @param AttributeMappingFactory $attributeMappingFactory
     * @param AttributeManagementInterface $attributeManagement
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        AttributeFactory $attributeFactory,
        AttributeOptionFactory $attributeOptionFactory,
        AttributeMappingFactory $attributeMappingFactory,
        AttributeManagementInterface $attributeManagement
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->attributeFactory = $attributeFactory;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->attributeMappingFactory = $attributeMappingFactory;
        $this->attributeManagement = $attributeManagement;
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

            if (!empty($petition = $this->getRequest()->getParam('petition')) && !empty($rowId = $this->getRequest()->getParam('rowId'))) {

                switch ($petition) {
                    case 'attributes':
                        $class = $this->getRequest()->getParam('rowClass');
                        $rowClass = !empty($class) ? 'class="dynamic-row ' . $class . '"' : 'dynamic-row';

                        $row = '
                            <tr ' . $rowClass . ' data-dynamic-id="' . $rowId . '">
                                <td class="align-middle"><div class="data-grid-cell-content">' . $this->getMadktingAttributes($rowId) . '</div></td>
                                <td class="align-middle"><div class="data-grid-cell-content">' . $this->getMagentoAttributes($rowId) . '</div></td>
                                <td class="align-middle"><div class="data-grid-cell-content default-options-content">' . __('Choose Yuju\'s attribute first') . '</div></td>
                                <td class="data-grid-actions-cell align-middle"><div class="data-grid-cell-content"><a class="dynamic-delete-action" href="#">' . __('Delete') . '</a></div></td>
                            </tr>
                        ';

                        $response = [
                            'error' => false,
                            'row' => $row
                        ];
                        break;
                    case 'options':
                        $response = [
                            'error' => false,
                            'options' => $this->getDefaultValueInput($rowId)
                        ];
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

    /**
     * @param int $rowId
     * @return string
     */
    protected function getMadktingAttributes($rowId)
    {
        $attributeSetId = $this->getRequest()->getParam('attributeSet');

        $attribute = $this->attributeFactory->create();

        /**
         * Get attribute mapping data
         *
         * @var \Madkting\Connect\Model\AttributeMapping[] $attributeMappingData
         */
        $attributeMapping = $this->attributeMappingFactory->create();
        $attributeMappingData = $attributeMapping
            ->getCollection()
            ->addFieldToFilter('attribute_set_id', $attributeSetId);

        if (empty($attributeMappingData->getData())) {

            /**
             * Get attribute options
             *
             * @var \Madkting\Connect\Model\Attribute[] $madktingAttributes
             */
            $madktingAttributes = $attribute
                ->getCollection()
                ->addFieldToFilter('requirement', array('neq' => 'required'))
                ->setOrder('attribute_label', Collection::SORT_ORDER_ASC);

            $madktingAttributeOptions = '<option value="">' . __('Choose an option') . '</option>';
            foreach ($madktingAttributes as $attribute) {
                if (in_array($attribute->getAttributeCode(), $attribute->madktingFieldsToOmit)) {
                    continue;
                }
                $madktingAttributeOptions .= '<option value="' . $attribute->getId() . '">' . $attribute->getAttributeLabel() . '</option>';
            }
        } else {

            $attributesMapped = [];
            foreach ($attributeMappingData as $attributeMapping) {
                $attributesMapped[] = $attributeMapping->getMadktingAttributeId();
            }
            
            /**
             * Get attribute options
             *
             * @var \Madkting\Connect\Model\Attribute[] $madktingAttributes
             */
            $madktingAttributes = $attribute
                ->getCollection()
                ->addFieldToFilter('attribute_id', array('nin' => $attributesMapped))
                ->setOrder('attribute_label', Collection::SORT_ORDER_ASC);

            $madktingAttributeOptions = '<option value="">' . __('Choose an option') . '</option>';
            foreach ($madktingAttributes as $attribute) {
                if (in_array($attribute->getAttributeCode(), $attribute->madktingFieldsToOmit)) {
                    continue;
                }
                $madktingAttributeOptions .= '<option value="' . $attribute->getId() . '">' . $attribute->getAttributeLabel() . '</option>';
            }
        }

        return '<select id="madkting-dynamic' . $rowId . '" class="admin__control-select madkting-attribute" name="attribute-dynamic' . $rowId . '[madktingId]">' . $madktingAttributeOptions . '</select>';
    }

    /**
     * @param int $rowId
     * @return string
     */
    protected function getMagentoAttributes($rowId)
    {
        /* Get magento's attributes */
        $magentoAttributes = $this->attributeManagement->getAttributes(ProductAttributeInterface::ENTITY_TYPE_CODE, $this->getRequest()->getParam('attributeSet'));

        /* Create array to sort */
        $magentoAttributeArray = [];
        foreach ($magentoAttributes as $attribute) {
            if (empty($attribute->getDefaultFrontendLabel())) {
                continue;
            }
            $magentoAttributeArray[$attribute->getAttributeId()] = $attribute->getDefaultFrontendLabel();
        }
        asort($magentoAttributeArray);

        $magentoAttributeOptions = '<option value="">' . __('Set as default') . '</option>';
        foreach ($magentoAttributeArray as $id => $label) {
            $magentoAttributeOptions .= '<option value="' . $id . '">' . $label . '</option>';
        }

        return '<select id="magento-dynamic' . $rowId . '" class="admin__control-select" name="attribute-dynamic' . $rowId . '[magentoId]">' . $magentoAttributeOptions . '</select>';
    }

    /**
     * @param int $rowId
     * @return string
     */
    protected function getDefaultValueInput($rowId)
    {
        $attributeId = $this->getRequest()->getParam('attributeId');

        $attribute = $this->attributeFactory->create();

        $attributeFormat = $attribute->load($attributeId)->getAttributeFormat();

        switch ($attributeFormat) {
            case 'textarea':
                $input = '<textarea id="default-dynamic' . $rowId . '" class="admin__control-textarea default-value" name="attribute-dynamic' . $rowId . '[defaultValue]"></textarea>';
                break;
            case 'select':
                $attributeOption = $this->attributeOptionFactory->create();

                /**
                 * Get attribute options
                 *
                 * @var \Madkting\Connect\Model\AttributeOption[] $optionsData
                 */
                $optionsData = $attributeOption
                    ->getCollection()
                    ->addFieldToFilter('attribute_id', $attributeId)
                    ->setOrder('sort_order', Collection::SORT_ORDER_ASC);

                $options = '<option value="">' . __('Choose an option') . '</option>';
                foreach ($optionsData as $option) {
                    $options .= '<option value="' . $option->getOptionValue() . '">' . $option->getOptionLabel() . '</option>';
                }

                $input = '<select id="default-dynamic' . $rowId . '" class="admin__control-select default-value" name="attribute-dynamic' . $rowId . '[defaultValue]">' . $options . '</select>';
                break;
            default:
                $input = '<input id="default-dynamic' . $rowId . '" class="admin__control-text default-value" name="attribute-dynamic' . $rowId . '[defaultValue]" type="text" />';
        }

        return $input;
    }

    /*
	 * Check permission via ACL resource
	 */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Madkting_Connect::attributes');
    }
}
