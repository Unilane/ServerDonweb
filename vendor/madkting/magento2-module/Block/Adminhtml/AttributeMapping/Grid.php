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

namespace Madkting\Connect\Block\Adminhtml\AttributeMapping;

use Madkting\Connect\Block\Adminhtml\MadktingGrid;
use Madkting\Connect\Helper\Data as MadktingHelper;
use Madkting\Connect\Model\AttributeFactory;
use Madkting\Connect\Model\AttributeOptionFactory;
use Madkting\Connect\Model\AttributeMappingFactory;
use Magento\Backend\Block\Widget\Context;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Framework\Data\Collection;

/**
 * Class Grid
 * @package Madkting\Connect\Block\Adminhtml\AttributeMapping
 */
class Grid extends MadktingGrid
{
    /**
     * @var string
     */
    protected $gridName = 'attribute-mapping';

    /**
     * @var bool
     */
    protected $isForm = true;

    /**
     * @var string
     */
    protected $formPath = '*/attributemapping/save';

    /**
     * @var array
     */
    protected $magentoAttributeArray;

    /**
     * @var bool
     */
    protected $defaultReference = false;

    /**
     * Magento's default codes for Yuju's attributes
     *
     * @var array
     */
    protected $magentoDefaultCodes = [
        'sku_simple' => 'sku',
        'sku' => 'sku',
        'name' => 'name',
        'description' => 'description',
        'price' => 'price',
        'brand' => 'brand'
    ];

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Madkting\Connect\Model\Attribute
     */
    protected $attribute;

    /**
     * @var \Madkting\Connect\Model\AttributeOption
     */
    protected $attributeOption;

    /**
     * @var \Madkting\Connect\Model\AttributeMapping
     */
    protected $attributeMapping;

    /**
     * @var AttributeManagementInterface
     */
    protected $attributeManagement;

    /**
     * @var MadktingHelper
     */
    protected $madktingHelper;

    /**
     * Grid constructor
     *
     * @param Context $context
     * @param ProductFactory $product
     * @param AttributeFactory $attributeFactory
     * @param AttributeOptionFactory $attributeOptionFactory
     * @param AttributeMappingFactory $attributeMappingFactory
     * @param AttributeManagementInterface $attributeManagement
     * @param MadktingHelper $madktingHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductFactory $product,
        AttributeFactory $attributeFactory,
        AttributeOptionFactory $attributeOptionFactory,
        AttributeMappingFactory $attributeMappingFactory,
        AttributeManagementInterface $attributeManagement,
        MadktingHelper $madktingHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->product = $product->create();
        $this->attribute = $attributeFactory->create();
        $this->attributeOption = $attributeOptionFactory->create();
        $this->attributeMapping = $attributeMappingFactory->create();
        $this->attributeManagement = $attributeManagement;
        $this->madktingHelper = $madktingHelper;

        /* Add grid buttons */
        $this->addButtons();

        /* Add columns name */
        $this->setColumns();

        /* Add rows */
        $this->setRows();
    }

    /**
     * Get active attribute set
     *
     * @return int
     */
    protected function getActiveAttributeSetId()
    {
        $attributeSetId = $this->getRequest()->getParam('attributeSetId');

        return $attributeSetId ? $attributeSetId : $this->getDefaultAttributeSetId();
    }

    /**
     * Get default attribute set ID
     *
     * @return int
     */
    protected function getDefaultAttributeSetId()
    {
        return $this->product->getDefaultAttributeSetId();
    }

    /**
     * Add grid buttons
     */
    protected function addButtons()
    {
        /* Add buttons */
        $this->addButton(
            'new-attribute-mapping',
            [
                'label' => __('Add Attribute'),
                'class' => 'action-secondary'
            ]
        );

        $this->addButton(
            'save-attribute-mapping',
            [
                'label' => __('Save Attribute Match'),
                'class' => 'primary'
            ]
        );
    }

    /**
     * Set table columns
     */
    protected function setColumns()
    {
        $this->addColumn('madkting_attribute', __('Yuju\'s Attribute'));
        $this->addColumn('magento_attribute', __('Magento\'s Attribute'));
        $this->addColumn('default_value', __('Default Value'));
        $this->addColumn('grid_actions', __('Actions'));
    }

    /**
     * Set grid rows
     */
    protected function setRows()
    {
        /**
         * Get attribute match data
         *
         * @var \Madkting\Connect\Model\AttributeMapping[] $attributeMapping
         */
        $attributeMapping = $this->attributeMapping
            ->getCollection()
            ->addFieldToFilter('attribute_set_id', $this->getActiveAttributeSetId());

        if (empty($attributeMapping->getData())) {

            /* Get default match */
            $attributeMapping = $this->attributeMapping
                ->getCollection()
                ->addFieldToFilter('attribute_set_id', $this->getDefaultAttributeSetId());

            empty($attributeMapping->getData()) ?: $this->defaultReference = true;
        }

        if (empty($attributeMapping->getData())) {

            /**
             * Get madkting attributes
             *
             * @var \Madkting\Connect\Model\Attribute[] $madktingAttributes
             */
            $madktingAttributes = $this->attribute
                ->getCollection()
                ->addFieldToFilter(['requirement', 'attribute_code'], [['eq' => 'Required'], ['in' => $this->madktingHelper->getAlwaysShown()]])
                ->setOrder('sort_order', Collection::SORT_ORDER_ASC);

            /* Add initial match data */
            foreach ($madktingAttributes as $attribute) {
                if (in_array($attribute->getAttributeCode(), $this->attribute->madktingFieldsToOmit)) {
                    continue;
                }

                /* Magento column special values */
                if ($attribute->getAttributeCode() == 'stock') {
                    $magentoValue = __('It will use product stock');
                } else {
                    $magentoValue = $this->getMagentoAttributes($attribute->getId(), null, $attribute->getAttributeCode());
                }

                /* Attribute's available actions */
                $actions = '';
                if ($attribute->getRequirement() != 'Required') {
                    $actions = '<a class="dynamic-delete-action" href="#">' . __('Delete') . '</a>';
                }

                $this->addRow([
                    [
                        'columnCode' => 'madkting_attribute',
                        'value' => $this->getMadktingAttributeLabel($attribute->getId(), $attribute->getAttributeLabel())
                    ],
                    [
                        'columnCode' => 'magento_attribute',
                        'value' => $magentoValue
                    ],
                    [
                        'columnCode' => 'default_value',
                        'value' => $this->getDefaultValueInput($attribute->getId(), $attribute->getAttributeFormat(), $attribute->getDefaultValue())
                    ],
                    [
                        'columnCode' => 'grid_actions',
                        'value' => $actions,
                        'class' => 'data-grid-actions-cell'
                    ]
                ]);
            }
        } else {
            foreach ($attributeMapping as $attribute) {

                /* Get Madkting attribute info */
                $madktingAttribute = $this->attribute->load($attribute->getMadktingAttributeId());

                /* Magento column special values */
                if ($madktingAttribute->getAttributeCode() == 'stock') {
                    $magentoValue = __('It will use product stock');
                } else {
                    $magentoValue = $this->getMagentoAttributes($madktingAttribute->getId(), $attribute->getMagentoAttributeId());
                }

                /* Attribute's available actions */
                $actions = '';
                if ($madktingAttribute->getRequirement() != 'Required') {
                    $class = $this->defaultReference ? 'dynamic-delete-action' : 'delete-action';
                    $actions = '<a class="' . $class . '" href="#">' . __('Delete') . '</a>';
                }

                $this->addRow([
                    [
                        'columnCode' => 'madkting_attribute',
                        'value' => $this->getMadktingAttributeLabel($madktingAttribute->getId(), $madktingAttribute->getAttributeLabel())
                    ],
                    [
                        'columnCode' => 'magento_attribute',
                        'value' => $magentoValue
                    ],
                    [
                        'columnCode' => 'default_value',
                        'value' => $this->getDefaultValueInput($madktingAttribute->getId(), $madktingAttribute->getAttributeFormat(), $attribute->getDefaultValue())
                    ],
                    [
                        'columnCode' => 'grid_actions',
                        'value' => $actions,
                        'class' => 'data-grid-actions-cell'
                    ]
                ]);
            }
        }
    }

    /**
     * @param int $id
     * @param string $label
     * @return string
     */
    protected function getMadktingAttributeLabel($id, $label)
    {
        $label = '<span>' . $label . '</span><input id="madkting' . $id . '" name="attribute' . $id . '[madktingId]" value="' . $id . '" type="hidden" />';

        return $label;
    }

    /**
     * @param int $madktingId
     * @param null|int $optionSelected
     * @param bool $useDefault
     * @return string
     */
    protected function getMagentoAttributes($madktingId, $optionSelected = null, $useDefault = false)
    {
        if (empty($this->magentoAttributeArray)) {
            /* Get magento's attributes */
            $magentoAttributes = $this->attributeManagement->getAttributes(ProductAttributeInterface::ENTITY_TYPE_CODE, $this->getActiveAttributeSetId());

            /* Create array to sort */
            $magentoAttributeArray = [];
            foreach ($magentoAttributes as $attribute) {
                if (empty($attribute->getDefaultFrontendLabel())) {
                    continue;
                }
                $magentoAttributeArray[$attribute->getAttributeId()] = [
                    'code' => $attribute->getAttributeCode(),
                    'label' => $attribute->getDefaultFrontendLabel()
                ];
            }
            asort($magentoAttributeArray);

            $this->magentoAttributeArray = $magentoAttributeArray;
        }

        $magentoAttributeOptions = '<option value="">' . __('Set as default') . '</option>';
        foreach ($this->magentoAttributeArray as $id => $info) {
            $selected = '';
            if (!empty($optionSelected)) {
                $selected = $optionSelected != $id ?: 'selected="selected"';
            } elseif (!empty($useDefault)) {
                if (!empty($this->magentoDefaultCodes[$useDefault])) {
                    $selected = $this->magentoDefaultCodes[$useDefault] != $info['code'] ?: 'selected="selected"';
                }
            }
            $magentoAttributeOptions .= '<option value="' . $id . '" ' . $selected . '>' . $info['label'] . '</option>';
        }

        return '<select id="magento' . $madktingId . '" class="admin__control-select" name="attribute' . $madktingId . '[magentoId]">' . $magentoAttributeOptions . '</select>';
    }

    /**
     * @param int $attributeId
     * @param string $attributeFormat
     * @param null|string $value
     * @return string
     */
    protected function getDefaultValueInput($attributeId, $attributeFormat, $value = null)
    {
        switch ($attributeFormat) {
            case 'textarea':
                $input = '<textarea id="default' . $attributeId . '" class="admin__control-textarea default-value" name="attribute' . $attributeId . '[defaultValue]">' . $value . '</textarea>';
                break;
            case 'select':

                /**
                 * Get attribute options
                 *
                 * @var \Madkting\Connect\Model\AttributeOption[] $optionsData
                 */
                $optionsData = $this->attributeOption
                    ->getCollection()
                    ->addFieldToFilter('attribute_id', $attributeId)
                    ->setOrder('sort_order', Collection::SORT_ORDER_ASC);

                $options = '<option value="">' . __('Choose an option') . '</option>';
                foreach ($optionsData as $option) {
                    $selected = $option->getOptionValue() == $value ? 'selected="selected"' : '';
                    $options .= '<option value="' . $option->getOptionValue() . '" ' . $selected . '>' . $option->getOptionLabel() . '</option>';
                }

                $input = '<select id="default' . $attributeId . '" class="admin__control-select default-value" name="attribute' . $attributeId . '[defaultValue]">' . $options . '</select>';
                break;
            default:
                $input = '<input id="default' . $attributeId . '" class="admin__control-text default-value" name="attribute' . $attributeId . '[defaultValue]" value="'. $value .'" type="text" />';
        }

        return $input;
    }

    public function getGrid()
    {
        $grid = '';

        if ($this->defaultReference) {
            $grid .= '<div class="messages"><div class="message"><div data-ui-id="messages-message-success"> ' . __('Attribute set selected has no attribute match, so default attribute set configuration is shown as reference. <b>DO NOT FORGET TO SAVE YOUR CHANGES</b>') . ' </div></div></div>';
        }

        $grid .= parent::getGrid();

        return $grid;
    }
}
