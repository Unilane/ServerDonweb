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

namespace Madkting\Connect\Block\System\Config\Form\Field;
use Madkting\Connect\Model\Config;
use Magento\Backend\Block\Template\Context;

/**
 * Class SyncAll
 * @package Madkting\Connect\Block\System\Config\Form\Field
 */
class SyncStockPrices extends GenericSyncBlock
{

    protected $_template = "Madkting_Connect::system/config/form/field/sync_stock_prices.phtml";

    /**
     * Remove scope label
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Generate synchronize all button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        if($this->validateLastUpdate()){
            $button = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'id' => 'sync_stock_prices_button',
                    'label' => __('Update'),
                    'data_attribute' => [
                        'mage-init' => [
                            'Madkting_Connect/js/queue-up-products' => [
                                'form_key' => $this->getFormKey(),
                                'button' => '#sync_stock_prices_button',
                                'fields' => json_encode(['stock','price']),
                                'url' => $this->getUrl('madkting/product/queueup')
                            ]
                        ]
                    ]
                ]
            );

            return $button->toHtml();
        }

        $hours = date("G", $this->config->getSyncTimeLeft());
        $minutes = date("i", $this->config->getSyncTimeLeft());
        $note = '<p class="comment">';
        $note .= "<span>". __(
                'Next massive synchronization will be available in %1 %3 and %2 %4.',
                $hours,
                $minutes,
                $hours > 1 ? __('hours') : __('hour'),
                $minutes > 1 ? __('minutes') : __('minute') ) . "</span>";
        $note .= '</p>';

        return $note;
    }

}