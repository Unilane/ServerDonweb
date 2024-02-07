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

namespace Madkting\Connect\Controller\Webhook;

use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\ProcessedFeed;
use Madkting\Connect\Model\Sales\Order;
use Madkting\Connect\Model\Task\ProcessFeed;
use Madkting\Exception\MadktingException;
use Madkting\MadktingClient;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NotFoundException;

/**
 * Class Listener
 * @package Madkting\Connect\Controller\Webhook
 */
class Listener extends Action
{
    /**
     * Madkting event key
     */
    const MADKTING_EVENT = 'event';

    /**
     * Madkting location key
     */
    const MADKTING_LOCATION = 'location';

    /**
     * @var Config
     */
    protected $madktingConfig;

    /**
     * @var Order
     */
    protected $orderManage;

    /**
     * @var ProcessFeed
     */
    protected $processFeed;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * Listener constructor.
     * @param Context $context
     * @param Config $madktingConfig
     * @param Order $orderManage
     * @param ProcessFeed $processFeed
     * @param MadktingLogger $logger
     */
    public function __construct(
        Context $context,
        Config $madktingConfig,
        Order $orderManage,
        ProcessFeed $processFeed,
        MadktingLogger $logger
    ) {
        parent::__construct($context);
        $this->madktingConfig = $madktingConfig;
        $this->orderManage = $orderManage;
        $this->processFeed = $processFeed;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($this->getRequest()->isPost()) {

            /* Get Madkting token */
            $token = $this->madktingConfig->getMadktingToken();
            if (!empty($token)) {
                try {
                    $client = new MadktingClient(['token' => $token]);
                    $serviceHook = $client->serviceHook();
                    $hookData = $serviceHook->detect();

                    if (!empty($hookData['location'])) {
                        /* Log Webhook location */
                        $this->logger->info(__('Webhook Received: %1', $hookData['location']));

                        /* Get webhook event */
                        switch ($hookData['event']){
                            case ProcessedFeed::EVENT_ORDER:
                                if ($this->madktingConfig->isSynchronizeOrdersEnabled()) {
                                    try {
                                        /* Get Madkting order data */
                                        $madktingOrderData = $client->exec($hookData['location']);
                                        $serviceOrder = $client->serviceOrder();
                                        $madktingOrderStatus = $serviceOrder->getStatus($madktingOrderData);
                                        $madktingOrderPaid = $serviceOrder->isPaid($madktingOrderData);

                                        /* Create/Update order */
                                        $this->orderManage->execute($madktingOrderData, $madktingOrderStatus, $madktingOrderPaid);

                                        echo 'Ok';
                                    } catch (MadktingException $e) {
                                        $this->logger->exception($e, __('Fatal error could not get order data, %1', $e->getMessage()), [
                                            'location' => $hookData['location']
                                        ]);
                                        echo $e->getMessage();
                                    } catch (\Exception $e) {
                                        $pk = !empty($madktingOrderData->pk) ? $madktingOrderData->pk : null;
                                        $reference = !empty($madktingOrderData->reference) ? $madktingOrderData->reference : null;
                                        $title = __('Order %1(%2) Error', $reference, $pk);
                                        $message = $e->getMessage();
                                        $sendMail = true;

                                        /* Omit duplicate PK email */
                                        if (stripos($message, 'constraint violation') !== false) {
                                            $sendMail = false;
                                        }

                                        $this->logger->exception($e, $message, [
                                            'title' => $title,
                                            'location' => $hookData['location']
                                        ], $sendMail);
                                        echo $message;
                                    }
                                }
                                break;
                            case ProcessedFeed::EVENT_PRODUCT:
                                try {
                                    /* Get product feed data */
                                    $madktingProductFeedData = $client->exec($hookData['location']);

                                    /* Process feed */
                                    $this->processFeed->execute($madktingProductFeedData, $hookData['location'], true);

                                    echo 'Ok';
                                } catch (MadktingException $e) {
                                    $this->logger->exception($e, __('Fatal error could not get product data, %1', $e->getMessage()), [
                                        'location' => $hookData['location']
                                    ]);
                                    echo $e->getMessage();
                                } catch (\Exception $e) {
                                    $this->logger->exception($e, $e->getMessage(), [
                                        'title' => __('Product Webhook Error'),
                                        'location' => $hookData['location']
                                    ]);
                                    echo $e->getMessage();
                                }
                                break;
                            default:
                                $message = __('Madkting webhook event could not be processed: %1', $hookData['event']);
                                $this->logger->debug($message);
                                echo $message;
                        }
                    } else {
                        $message = __('Madkting webhook has not location information');
                        $this->logger->debug($message);
                        echo $message;
                    }
                } catch (\Exception $e) {
                    $eData = $e->getMessage() . ' ' . $e->getFile() . '(' . $e->getLine() . ')';
                    $this->logger->exception($e, __('Madkting webhook general error, %1', $eData));
                    echo $eData;
                } catch (\Throwable $t) {
                    $tData = $t->getMessage() . ' ' . $t->getFile() . '(' . $t->getLine() . ')';
                    $this->logger->exception($t, __('Madkting webhook code error, %1', $tData));
                    echo $tData;
                }
            } else {
                $message = __('There is no Yuju token information');
                $this->logger->error($message);
                echo $message;
            }
        } else {
            throw new NotFoundException(__('Petition is not POST'));
        }
    }
}
