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

namespace Madkting\Connect\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Attribute
 * @package Madkting\Connect\Model
 *
 * @method Attribute setAttributeId(\int $attributeId)
 * @method Attribute setAttributeCode(\string $attributeCode)
 * @method Attribute setAttributeLabel(\string $attributeLabel)
 * @method Attribute setAttributeFormat(\string $attributeFormat)
 * @method Attribute setMaxLength(\int $maxLength)
 * @method Attribute setMinNum(\int $minNum)
 * @method Attribute setMaxNum(\int $maxNum)
 * @method Attribute setDefaultValue(\string $defaultValue)
 * @method Attribute setRequirement(\string $requirement)
 * @method Attribute setInVariation(\bool $inVariation)
 * @method Attribute setHasOptions(\bool $hasOptions)
 * @method Attribute setTooltip(\string $tooltip)
 * @method Attribute setSortOrder(\int $sortOrder)
 * @method Attribute setCreatedAt(\string $createdAt)
 * @method Attribute setUpdatedAt(\string $updatedAt)
 * @method int getAttributeId()
 * @method string getAttributeCode()
 * @method string getAttributeLabel()
 * @method string getAttributeFormat()
 * @method int getMaxLength()
 * @method int getMinNum()
 * @method int getMaxNum()
 * @method string getDefaultValue()
 * @method string getRequirement()
 * @method bool getInVariation()
 * @method bool getHasOptions()
 * @method string getTooltip()
 * @method int getSortOrder()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 */
class Attribute extends AbstractModel implements IdentityInterface
{
    /**
     * Fields to omit as these are going to be defined later specially
     *
     * @var array
     */
    public $madktingFieldsToOmit = [
        'custom_cat',
        'images',
        'link',
        'custom_variation_name',
        'custom_variation_value'
    ];

    /**
     * Cache tag
     */
    const CACHE_TAG = 'madkting_attribute';

    /**
     * @var string
     */
    protected $_cacheTag = 'madkting_attribute';

    /**
     * @var string
     */
    protected $_eventPrefix = 'madkting_attribute';

    /**
     * Attribute construct
     */
    protected function _construct()
    {
        $this->_init('Madkting\Connect\Model\ResourceModel\Attribute');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
