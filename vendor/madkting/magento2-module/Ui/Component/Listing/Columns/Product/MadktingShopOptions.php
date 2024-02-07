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

namespace Madkting\Connect\Ui\Component\Listing\Columns\Product;

use Madkting\Connect\Helper\Data;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MadktingShopOptions
 * @package Madkting\Connect\Ui\Component\Listing\Columns\Product
 */
class MadktingShopOptions implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $shops;

    /**
     * @var Data
     */
    protected $madktingHelper;

    /**
     * MadktingShopOptions constructor
     *
     * @param Data $madktingHelper
     */
    public function __construct(Data $madktingHelper)
    {
        $this->madktingHelper = $madktingHelper;
        $this->getMadktingShops();
    }


    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        if (!empty($this->shops)) {
            foreach ($this->shops as $value => $label) {
                $data['value'] = $value;
                $data['label'] = $label;

                $options[] = $data;
            }
        }

        return $options;
    }

    /**
     * Get Madkting Shops available for token
     */
    public function getMadktingShops()
    {
        $madktingShops = $this->madktingHelper->getMadktingShops();
        foreach ($madktingShops as $shop) {
            $this->shops[$shop->pk] = $shop->name;
        }
    }
}
