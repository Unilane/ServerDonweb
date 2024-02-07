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

namespace Madkting\Connect\Block\System\Config\Form\Field;

use Madkting\Connect\Model\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ValidToken
 * @package Madkting\Connect\Block\System\Config\Form\Field
 */
class ValidToken extends Field
{
    /**
     * @var Config
     */
    protected $madktingConfig;

    /**
     * @param Context $context
     * @param Config $madktingConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $madktingConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->madktingConfig = $madktingConfig;
    }

    /**
     * @inheritdoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        /* Validate token */
        $token = $this->madktingConfig->getMadktingToken();
        if (!empty($token)) {
            $value = __('Valid Account');
        } else {
            $value = __('Invalid Account');
        }

        $element->setValue($value);
        return $element->getElementHtml();
    }
}
