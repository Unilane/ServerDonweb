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

namespace Madkting\Connect\Model\Task;

use Madkting\Connect\Logger\MadktingLogger;
use Madkting\Connect\Model\Config as MadktingConfig;
use Madkting\Connect\Model\ResourceModel\ProcessedFeed\CollectionFactory as ProcessedFeedCollectionFactory;
use Madkting\MadktingClient;

/**
 * Class ConsultFeed
 * @package Madkting\Connect\Model\Task
 */
class ConsultFeed
{
    /**
     * @var MadktingConfig
     */
    protected $madktingConfig;

    /**
     * @var \Madkting\Connect\Model\ResourceModel\ProcessedFeed\Collection
     */
    protected $processedFeedCollection;

    /**
     * @var ProcessFeedFactory
     */
    protected $processFeedFactory;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * ConsultFeed constructor
     *
     * @param MadktingConfig $madktingConfig
     * @param ProcessedFeedCollectionFactory $processedFeedCollectionFactory
     * @param ProcessFeedFactory $processFeedFactory
     * @param MadktingLogger $logger
     */
    public function __construct(
        MadktingConfig $madktingConfig,
        ProcessedFeedCollectionFactory $processedFeedCollectionFactory,
        ProcessFeedFactory $processFeedFactory,
        MadktingLogger $logger
    ) {
        $this->madktingConfig = $madktingConfig;
        $this->processedFeedCollection = $processedFeedCollectionFactory->create();
        $this->processFeedFactory = $processFeedFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        /* Processed feeds success/error count */
        $feedsSuccess = 0;
        $feedsError = 0;

        try {
            /** @var \Madkting\Connect\Model\ResourceModel\ProcessedFeed\Collection $unfinishedFeeds */
            $unfinishedFeeds = $this->processedFeedCollection->addFieldToFilter('status', ProcessFeed::MADKTING_FEED_WAIT);
            if (!empty($unfinishedFeeds->getData())) {
                /* Get Madkting token and get Madkting Client */
                $token = $this->madktingConfig->getMadktingToken();
                $client = new MadktingClient(['token' => $token]);

                foreach ($unfinishedFeeds as $feed) {
                    try {
                        /* Process feed */
                        $location = $feed->getLocation();
                        $madktingProductFeedData = $client->exec($location);
                        $this->processFeedFactory->create()->execute($madktingProductFeedData, $location);
                        ++$feedsSuccess;
                    } catch (\Exception $e) {
                        $this->logger->exception($e, __('Error while consulting feed %1: %2', $feed->getId(), $e->getMessage()));
                        ++$feedsError;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->exception($e, __('Error while consulting feeds: %1', $e->getMessage()));
            ++$feedsError;
        }

        return [
            'success' => $feedsSuccess,
            'error' => $feedsError
        ];
    }
}
