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

namespace Madkting\Connect\Controller\Adminhtml\OrderStatus;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Madkting\Connect\Model\OrderStatusFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class InlineEdit
 * @package Madkting\Connect\Controller\Adminhtml\OrderStatus
 */
class InlineEdit extends Action
{
    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var OrderStatusFactory
     */
    protected $orderStatusFactory;

    /**
     * InlineEdit constructor.
     * @param Action\Context $context
     * @param JsonFactory $jsonFactory
     * @param OrderStatusFactory $orderStatusFactory
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $jsonFactory,
        OrderStatusFactory $orderStatusFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->orderStatusFactory = $orderStatusFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $statusItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->isAjax() && count($statusItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct sent data.')],
                'error' => true,
            ]);
        }
        foreach ($statusItems as $statusId => $statusData) {
            /** @var \Madkting\Connect\Model\OrderStatus $status */
            $status = $this->orderStatusFactory->create()->load($statusId);
            try {
                $status->addData($statusData);
                $status->save();
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithPostId($status, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithPostId($status, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPostId(
                    $status,
                    __('Something went wrong while saving order status.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
