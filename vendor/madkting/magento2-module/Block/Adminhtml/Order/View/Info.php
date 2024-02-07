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

namespace Madkting\Connect\Block\Adminhtml\Order\View;

use Madkting\Connect\Helper\Data;
use Madkting\Connect\Model\OrderActionsFactory;
use Magento\Backend\Block\Template;
use Magento\Framework\Registry;

/**
 * Class Info
 * @package Madkting\Connect\Block\Adminhtml\Order\View
 */
class Info extends Template
{
    /**
     * @var string
     */
    protected $_template = 'order/view/info.phtml';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $madktingHelper;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var int
     */
    protected $marketplacePk;

    /**
     * @var OrderActionsFactory
     */
    protected $orderActionsFactory;

    /**
     * Info constructor.
     * @param Template\Context $context
     * @param Registry $registry
     * @param Data $madktingHelper
     * @param OrderActionsFactory $orderActionsFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Data $madktingHelper,
        OrderActionsFactory $orderActionsFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->madktingHelper = $madktingHelper;
        $this->orderActionsFactory = $orderActionsFactory;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if (empty($this->order)) {
            $this->order = $this->registry->registry('current_order');
        }
        return $this->order;
    }

    /**
     * @return string
     */
    public function getMarketPlaceOrderPk()
    {
        return $this->getOrder()->getMadktingPk();
    }

    /**
     * @return string
     */
    public function getShopPk()
    {
        return $this->getOrder()->getMadktingShopPk();
    }

    /**
     * @return string
     */
    public function getMadktingStatus()
    {
        $madktingStatus = $this->getOrder()->getMadktingStatus();

        return $this->madktingHelper->getStatusByCode($madktingStatus);
    }

    /**
     * @return string
     */
    public function getMarketPlacePk()
    {
        return $this->getOrder()->getMadktingMarketplacePk();
    }

    /**
     * @return string
     */
    public function getMarketplace()
    {
        $marketplacePk = $this->getMarketPlacePk();

        return $this->madktingHelper->getMarketplaceByPk($marketplacePk);
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        $paymentCode = $this->getOrder()->getPayment()->getAdditionalInformation('madkting_payment');

        return $this->madktingHelper->getPaymentMethodByCode($paymentCode);
    }

    /**
     * @return array
     */
    public function getOrderActions()
    {
        $orderPk = $this->getMarketPlaceOrderPk();

        return $this->orderActionsFactory->create()->loadByOrderPk($orderPk);
    }

    /**
     * @param array $extra
     * @return string
     */
    public function getStatusUrlParams($extra = null)
    {
        $params = [
            'order_pk' => $this->getMarketPlaceOrderPk(),
            'shop' => $this->getShopPk(),
            'marketplace' => $this->getMarketPlacePk()
        ];

        if (!empty($extra)) {
            $params = array_merge($params, $extra);
        }

        return http_build_query($params);
    }
}
