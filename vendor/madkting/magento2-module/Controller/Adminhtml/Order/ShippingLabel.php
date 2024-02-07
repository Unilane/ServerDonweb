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

namespace Madkting\Connect\Controller\Adminhtml\Order;

use Madkting\Connect\Helper\Data;
use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Config;
use Madkting\Exception\MadktingException;
use Madkting\MadktingClient;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class ShippingLabel extends Action
{
    /**
     * Madkting order action name
     */
    const ORDER_ACTION = 'print_delivery_label';

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var Config
     */
    protected $madktingConfig;

    /**
     * @var Data
     */
    protected $madktingHelper;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * Configuration constructor.
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param Config $madktingConfig
     * @param Data $madktingHelper
     * @param MadktingLogger $logger
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        Config $madktingConfig,
        Data $madktingHelper,
        MadktingLogger $logger
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->madktingConfig = $madktingConfig;
        $this->madktingHelper = $madktingHelper;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $response = [
            'error' => true,
            'message' => ''
        ];

        if ($this->getRequest()->isAjax()) {

            /* If params have been provided */
            $params = $this->getRequest()->getParams();
            if (!empty($params['order_pk'])
                && !empty($params['shop'])
                && !empty($params['marketplace'])
                && !empty($params['format'])) {
                try {
                    /* Madkting client */
                    $token = $this->madktingConfig->getMadktingToken();
                    $client = new MadktingClient(['token' => $token]);
                    $orderService = $client->serviceOrder();
                    $location = $orderService->getUrlShippingLabel($params['shop'], $params['marketplace'], $params['order_pk'], $params['format']);

                    $response = [
                        'error' => false,
                        'action' => 'blank',
                        'location' => $location
                    ];

                    /* Set order action as done */
                    $this->madktingHelper->setOrderActionDone($params['order_pk'], self::ORDER_ACTION);
                } catch (\Exception $e) {
                    $logCase = $this->logger->exception($e, __('Download Shipping Label Error: %1', $e->getMessage()));
                    $response['message'] = __('Download shipping label failed, check log registry #%1 for more details', $logCase);
                }
            } else {
                $response['message'] = __('Download shipping label failed, must provide required parameters');
            }
        } else {
            $this->messageManager->addErrorMessage(__('Incorrect petition'));
            return $this->resultRedirectFactory->create()->setPath('admin');
        }

        $jsonResult = $this->jsonFactory->create();
        return $jsonResult->setData($response);
    }
}
