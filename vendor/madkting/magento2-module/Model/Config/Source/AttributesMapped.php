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

namespace Madkting\Connect\Model\Config\Source;

use Madkting\Connect\Model\ResourceModel\Attribute\CollectionFactory as AttributeCollectionFactory;
use Madkting\Connect\Model\ResourceModel\AttributeMapping\CollectionFactory as AttributeMappingCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Option\ArrayInterface;

class AttributesMapped implements ArrayInterface
{
    /**
     * @var AttributeMappingCollectionFactory
     */
    protected $mappingFactory;

    /**
     * @var AttributeCollectionFactory
     */
    protected $attributeFactory;

    public function __construct(
        AttributeMappingCollectionFactory $mappingFactory,
        AttributeCollectionFactory $attributeFactory
    ) {
        $this->mappingFactory = $mappingFactory;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => 'images',
                'label' => __('Images')
            ]
        ];

        /**
         * Get attributes matched
         *
         * @var \Madkting\Connect\Model\ResourceModel\AttributeMapping\Collection $mappingCollection
         */
        $mappingCollection = $this->mappingFactory->create();
        $attributeMapping = $mappingCollection
            ->setOrder('madkting_attribute_id', Collection::SORT_ORDER_ASC)
            ->getData();
        $attributesId = array_unique(array_column($attributeMapping, 'madkting_attribute_id'));

        /**
         * Get attribute matched information
         *
         * @var \Madkting\Connect\Model\ResourceModel\Attribute\Collection $attributeCollection
         */
        $attributeCollection = $this->attributeFactory->create();
        $attributes = $attributeCollection
            ->addFieldToFilter('attribute_id', ['in' => $attributesId])
            ->setOrder('attribute_label', Collection::SORT_ORDER_ASC)
            ->getData();
        foreach ($attributes as $attribute) {
            $options[] = ['value' => $attribute['attribute_code'], 'label' => $attribute['attribute_label']];
        }

        return $options;
    }
}
