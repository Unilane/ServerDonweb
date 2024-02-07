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
 * Class ProductImage
 * @package Madkting\Connect\Model
 *
 * @method ProductImage setMagentoImageUrl(\string $magentoImageUrl)
 * @method ProductImage setMagentoProductId(\int $magentoProductId)
 * @method ProductImage setMagentoImageId(\int $magentoImageId)
 * @method ProductImage setMadktingProductId(\string $madktingProductId)
 * @method ProductImage setMadktingImageId(\string $madktingImageId)
 * @method ProductImage setPosition(\int $magentoImagePosition)
 * @method ProductImage setCreatedAt(\string $createdAt)
 * @method ProductImage setUpdatedAt(\string $updatedAt)
 * @method string getMagentoImageUrl()
 * @method int getMagentoProductId()
 * @method int getMagentoImageId()
 * @method string getMadktingProductId()
 * @method string getMadktingImageId()
 * @method int getPosition()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 */
class ProductImage extends AbstractModel implements IdentityInterface
{
    /**
     * Cache Tag
     */
    const CACHE_TAG = "madkting_product_image";
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = "madkting_product_image";

    /**
     * Model constructor initialize resource model
     */
    public function _construct()
    {
        $this->_init(
            'Madkting\Connect\Model\ResourceModel\ProductImage'
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

    /**
     * Define the Id of image in Madkting using the url of the image in Magento
     *
     * @param string $mdkImagePk
     * @param string $mageImageUrl
     *
     * @return \Madkting\Connect\Model\ProductImage
     */
    public function setMadktingImageIdByUrl($mdkImagePk, $mageImageUrl)
    {
        $image = $this->load($mageImageUrl,'magento_image_url');
        $image->setData('madkting_image_id', $mdkImagePk);

        return $this;
    }
}
