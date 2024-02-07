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
 * @author Carlos Guillermo Jiménez Salcedo <guillermo@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Product
 * @package Madkting\Connect\Model
 *
 * @method Product setMagentoProductId(\int $magentoProductId)
 * @method Product setMadktingProductId(\string $madktingProductId)
 * @method Product setMadktingParentId(\string $madktingParentId)
 * @method Product setMagentoStoreId(\int $magentoStoreId)
 * @method Product setMadktingStoreId(\string $madktingStoreId)
 * @method Product setMadktingType(\int $madktingType)
 * @method Product setHasVariations(\bool $hasVariations)
 * @method Product setMadktingAttributes(\string $madktingAttribues)
 * @method Product setStatus(\int $status)
 * @method Product setStatusMessage(\string $status)
 * @method Product setCreatedAt(\string $createdAt)
 * @method Product setUpdatedAt(\string $updatedAt)
 * @method int getMagentoProductId()
 * @method string getMadktingProductId()
 * @method string getMadktingParentId()
 * @method int getMagentoStoreId()
 * @method string getMadktingStoreId()
 * @method int getMadktingType()
 * @method bool getHasVariations()
 * @method string getMadktingAttributes()
 * @method int getStatus()
 * @method string getStatusMessage()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 */
class Product extends AbstractModel implements IdentityInterface
{
    /**
     * Cache Tag
     */
    const CACHE_TAG = "madkting_product";

    /**
     * Madkting product type
     */
    const TYPE_PRODUCT = 1;
    const TYPE_VARIATION = 2;

    /**
     * Madkting product status id's
     */
    const STATUS_CREATING = 1;
    const STATUS_UPDATING = 2;
    const STATUS_DELETING = 3;
    const STATUS_SYNCHRONIZED = 4;
    const STATUS_ERROR = 5;
    const STATUS_WARNING = 6;

    /**
     * Madkting product images status
     */
    const STATUS_CREATING_IMAGES = 7;
    const STATUS_UPDATING_IMAGES = 8;
    const STATUS_DELETING_IMAGES = 9;

    /**
     * Madkting product parent status
     */
    const STATUS_PARENT_ERROR = 10;
    const STATUS_PARENT_WARNING = 11;

    /**
     * Madkting general system error
     */
    const STATUS_SYSTEM_ERROR = 12;

    /**
     * Madkting product type labels array
     */
    protected $types = [
        self::TYPE_PRODUCT => 'Product',
        self::TYPE_VARIATION => 'Variation'
    ];

    /**
     * Madkting product status labels array
     */
    protected $statuses = [
        self::STATUS_CREATING => 'Creating',
        self::STATUS_UPDATING => 'Updating',
        self::STATUS_DELETING => 'Deleting',
        self::STATUS_SYNCHRONIZED => 'Synchronized',
        self::STATUS_ERROR => 'Error',
        self::STATUS_WARNING => 'Warning',
        self::STATUS_CREATING_IMAGES => 'Creating Images',
        self::STATUS_UPDATING_IMAGES => 'Updating Images',
        self::STATUS_DELETING_IMAGES => 'Deleting Images',
        self::STATUS_PARENT_ERROR => 'Parent Error',
        self::STATUS_PARENT_WARNING => 'Parent Warning',
        self::STATUS_SYSTEM_ERROR => 'System Error'
    ];

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = "madkting_product";

    /**
     * Model Constructor
     */
    public function _construct()
    {
        $this->_init('Madkting\Connect\Model\ResourceModel\Product');
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
     * @param int $id
     * @return string
     */
    public function getStatusById($id)
    {
        if( isset( $this->statuses[$id] ) ) {
            return __( $this->statuses[$id] );
        }

        return false;
    }

    /**
     * @return array
     */
    public function getTypeArray()
    {
        $types = [];

        foreach ($this->types as $value => $label) {
            $types[$value] = __($label);
        }

        return $types;
    }

    /**
     * @return array
     */
    public function getStatusArray()
    {
        $statuses = [];

        foreach ($this->statuses as $value => $label) {
            $statuses[$value] = __($label);
        }

        return $statuses;
    }
}
