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
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CategoriesMapping
 * @package Madkting\Connect\Model
 *
 * @method CategoriesMapping setMagentoCategoryId(\int $magentoCategoryId)
 * @method CategoriesMapping setMadktingCategoryId(\int $madktingCategoryId)
 * @method CategoriesMapping setCreatedAt(\string $createdAt)
 * @method CategoriesMapping setUpdatedAt(\string $updatedAt)
 * @method int getMagentoCategoryId()
 * @method int getMadktingCategoryId()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 */
class CategoriesMapping extends AbstractModel implements IdentityInterface
{

    /**
     * Cache Tag
     */
    const CACHE_TAG = "madkting_mapping_categories";

    protected $_eventPrefix = "madkting_mapping_categories";

    /**
     * Model construct
     */
    public function _construct()
    {
        $this->_init(
            "Madkting\Connect\Model\ResourceModel\CategoriesMapping"
        );
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
}
