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

namespace Madkting\Connect\Block\Adminhtml\Order\View\Tab;

use Madkting\Connect\Block\Adminhtml\Order\View;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class MadktingInfo
 * @package Madkting\Connect\Block\Adminhtml\Order\View\Tab
 */
class Info extends View\Info implements TabInterface
{
    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return __('Yuju');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('Yuju Information');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        if (!empty($this->getMarketPlaceOrderPk())) {
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        if (!empty($this->getMarketPlaceOrderPk())) {
            return false;
        }
        return true;
    }
}
