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

namespace Madkting\Connect\Block\Adminhtml\AttributeOptionMapping;

use Madkting\Connect\Block\Adminhtml\MadktingGrid;
use Madkting\Connect\Model\AttributeOptionFactory;
use Madkting\Connect\Model\AttributeOptionMappingFactory;
use Madkting\Connect\Model\AttributeMappingFactory;
use Magento\Backend\Block\Widget\Context;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Data\Collection;

/**
 * Class Grid
 * @package Madkting\Connect\Block\Adminhtml\AttributeOptionMapping
 */
class Grid extends MadktingGrid
{
    /**
     * @var string
     */
    protected $gridName = 'attribute-option-mapping';

    /**
     * @var bool
     */
    protected $isForm = true;

    /**
     * @var string
     */
    protected $formPath = '*/attributeoptionmapping/save';

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var \Madkting\Connect\Model\AttributeOption
     */
    protected $attributeOption;

    /**
     * @var \Madkting\Connect\Model\AttributeOptionMapping
     */
    protected $attributeOptionMapping;

    /**
     * @var \Madkting\Connect\Model\AttributeMapping
     */
    protected $attributeMapping;

    /**
     * @var array
     */
    protected $madktingOptions = [];

    /**
     * Grid constructor
     *
     * @param Context $context
     * @param EavConfig $eavConfig
     * @param AttributeOptionFactory $attributeOptionFactory
     * @param AttributeOptionMappingFactory $attributeOptionMappingFactory
     * @param AttributeMappingFactory $attributeMappingFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        EavConfig $eavConfig,
        AttributeOptionFactory $attributeOptionFactory,
        AttributeOptionMappingFactory $attributeOptionMappingFactory,
        AttributeMappingFactory $attributeMappingFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->eavConfig = $eavConfig;
        $this->attributeOption = $attributeOptionFactory->create();
        $this->attributeOptionMapping = $attributeOptionMappingFactory->create();
        $this->attributeMapping = $attributeMappingFactory->create();

        /* Add grid buttons */
        $this->addButtons();

        /* Add columns name */
        $this->setColumns();

        /* Add rows */
        $this->setRows();
    }

    /**
     * Add grid buttons
     */
    protected function addButtons()
    {
        $this->addButton(
            'save-attribute-option-mapping',
            [
                'label' => __('Save Attribute Option Match'),
                'class' => 'primary'
            ]
        );
    }

    /**
     * Set table columns
     */
    protected function setColumns()
    {
        $this->addColumn('magento_attribute_option', __('Magento\'s Option'));
        $this->addColumn('madkting_attribute_option', __('Yuju\'s Option'));
    }

    /**
     * Set grid rows
     */
    protected function setRows()
    {
        $madktingId = $this->getRequest()->getParam('madktingId');
        $magentoId = $this->getRequest()->getParam('magentoId');

        if (empty($madktingId) || empty($magentoId)) {
            $selectOptionsMapped = $this->attributeMapping->getCollection()->getSelectableAttributes();
            if (!empty($selectOptionsMapped)) {
                $madktingId = $selectOptionsMapped[0]['madkting_id'];
                $magentoId = $selectOptionsMapped[0]['magento_id'];
            }
        }

        if (!empty($madktingId) && !empty($magentoId)) {

            /* Get Magento options */
            $options = $this->eavConfig->getAttribute(ProductAttributeInterface::ENTITY_TYPE_CODE, $magentoId)->getSource()->getAllOptions();
            foreach ($options as $option) {
                if (!empty($option['value'])) {
                    $this->addRow([
                        [
                            'columnCode' => 'magento_attribute_option',
                            'value' => $this->getMagentoOptionLabel($option['value'], $option['label'])
                        ],
                        [
                            'columnCode' => 'madkting_attribute_option',
                            'value' => $this->getMadktingOptions($option['value'], $madktingId)
                        ]
                    ]);
                }
            }
        }
    }

    /**
     * @param int $id
     * @param string $label
     * @return string
     */
    protected function getMagentoOptionLabel($id, $label)
    {
        $label = '<span>' . $label . '</span><input id="magento' . $id . '" name="option' . $id . '[magentoId]" value="' . $id . '" type="hidden" />';

        return $label;
    }

    /**
     * @param int $magentoOptionId
     * @param int $madktingAttributeId
     * @return string
     */
    protected function getMadktingOptions($magentoOptionId, $madktingAttributeId)
    {
        /* Get Madkting options */
        if (empty($this->madktingOptions)) {
            $this->madktingOptions = $this->attributeOption
                ->getCollection()
                ->addFieldToFilter('attribute_id', $madktingAttributeId)
                ->setOrder('sort_order', Collection::SORT_ORDER_ASC)
                ->getItems();
        }

        $madktingOptions = '<option value="">' . __('No match') . '</option>';
        foreach ($this->madktingOptions as $option) {

            /* Get match id if exists */
            $mappingId = $this->attributeOptionMapping
                ->getCollection()
                ->addFieldToFilter('madkting_attribute_option_id', $option->getId())
                ->addFieldToFilter('magento_attribute_option_id', $magentoOptionId)
                ->setPageSize(1)
                ->getFirstItem()
                ->getAttributeOptionMappingId();

            $selected = !empty($mappingId) ? 'selected="selected"' : '';
            $madktingOptions .= '<option value="' . $option->getId() . '" ' . $selected . '>' . $option->getOptionLabel() . '</option>';
        }

        return '<select id="madkting' . $magentoOptionId . '" class="admin__control-select" name="option' . $magentoOptionId . '[madktingId]">' . $madktingOptions . '</select>';
    }

    /**
     * Get attributes matched options
     *
     * @return array
     */
    public function getAttributeMapped()
    {
        $madktingOptions = '';
        $magentoOptions = '';

        $selectOptionsMapped = $this->attributeMapping->getCollection()->getSelectableAttributes();

        if (!empty($selectOptionsMapped)) {
            $firstOption = true;
            $madktingId = '';
            foreach ($selectOptionsMapped as $option) {
                if ($option['madkting_id'] != $madktingId) {
                    empty($madktingId)?:$firstOption = false;
                    $madktingId = $option['madkting_id'];
                    $madktingOptions .= '<option value="'. $madktingId . '">' . $option['madkting_label'] . '</option>';
                }

                if ($firstOption) {
                    $magentoOptions .= '<option value="'. $option['magento_id'] . '">' . $option['magento_label'] . '</option>';
                }
            }
        }

        if (empty($madktingOptions) || empty($magentoOptions)) {
            return [];
        }

        return [
            'madkting' => $madktingOptions,
            'magento' => $magentoOptions
        ];
    }
}
