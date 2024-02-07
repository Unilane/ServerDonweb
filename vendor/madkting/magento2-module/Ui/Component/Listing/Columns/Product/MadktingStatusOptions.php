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

use Madkting\Connect\Model\Product;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MadktingStatusOptions
 * @package Madkting\Connect\Ui\Component\Listing\Columns\Product
 */
class MadktingStatusOptions implements OptionSourceInterface
{
    /**
     * @var Product
     */
    protected $statuses;

    /**
     * MadktingStatusOptions constructor
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->statuses = $product->getStatusArray();
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
                'value' => NULL,
                'label' => __('No Synchronized')
            ],
            [
                'value' => 'ns',
                'label' => __('No Synchronized')
            ]
        ];

        if (!empty($this->statuses)) {
            foreach ($this->statuses as $value => $label) {
                $data['value'] = $value;
                $data['label'] = $label;

                $options[] = $data;
            }
        }

        return $options;
    }
}
