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

namespace Madkting\Connect\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Catalog\Model\Product\AttributeSet\Options as AttributeSetOptions;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class AttributeSetSelector
 * @package Madkting\Connect\Block\Adminhtml
 */
class AttributeSetSelector extends Template
{
    /**
     * @var AttributeSetOptions
     */
    protected $attributeSetOptions;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * AttributeSetSelector constructor
     *
     * @param Template\Context $context
     * @param AttributeSetOptions $attributeSetOptions
     * @param ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AttributeSetOptions $attributeSetOptions,
        ProductFactory $productFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeSetOptions = $attributeSetOptions;
        $this->product = $productFactory->create();
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->getHidden();
    }

    /**
     * Get attribute set options
     *
     * @return string
     */
    public function getAttributeSetOptions()
    {
        $options = '';

        $attributeSets = $this->attributeSetOptions->toOptionArray();

        $activeAttributeSet = $this->getRequest()->getParam('attributeSetId') ? $this->getRequest()->getParam('attributeSetId') : $this->product->getDefaultAttributeSetId();

        foreach ($attributeSets as $attributeSet) {
            $selected = $activeAttributeSet == $attributeSet['value'] ? 'selected="selected"' : '';
            $options .= '<option value="' . $attributeSet['value'] . '"' . $selected . '>' . $attributeSet['label'] . '</option>';
        }

        return $options;
    }
}
