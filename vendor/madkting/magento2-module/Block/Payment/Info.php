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

namespace Madkting\Connect\Block\Payment;

use Madkting\Connect\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Payment\Gateway\ConfigInterface;

/**
 * Class Info
 * @package Madkting\Connect\Block\Payment
 */
class Info extends ConfigurableInfo
{
    /**
     * @var string
     */
    protected $_template = 'Madkting_Connect::payment/info/madkting.phtml';

    /**
     * @var Data
     */
    protected $madktingHelper;

    /**
     * Info constructor.
     * @param Context $context
     * @param ConfigInterface $config
     * @param Data $madktingHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        Data $madktingHelper,
        array $data = []
    ) {
        parent::__construct($context, $config, $data);
        $this->madktingHelper = $madktingHelper;
    }

    /**
     * @return string
     */
    public function getMadktingPk()
    {
        return $this->getInfo()->getAdditionalInformation('madkting_pk');
    }

    /**
     * @return string
     */
    public function getMadktingMarketplace()
    {
        $marketplacePk = $this->getInfo()->getAdditionalInformation('madkting_marketplace_pk');

        return $this->madktingHelper->getMarketplaceByPk($marketplacePk);
    }

    /**
     * @return string
     */
    public function getMadktingPayment()
    {
        $paymentCode = $this->getInfo()->getAdditionalInformation('madkting_payment');

        return $this->madktingHelper->getPaymentMethodByCode($paymentCode);
    }

    /**
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Madkting_Connect::payment/info/pdf/madkting.phtml');
        return $this->toHtml();
    }
}
