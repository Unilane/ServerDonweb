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
use Madkting\Connect\Model\Config;
use Madkting\Connect\Model\ProcessedFeedFactory;
use Madkting\Connect\Model\ProductTaskQueue;
use Madkting\Connect\Model\ProductTaskQueueFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class CleanTask
 * @package Madkting\Connect\Model\Task
 */
class CleanTask
{
    /**
     * Seconds per minute
     */
    const SECONDS_PER_DAY = 86400;

    /**
     * @var ProductTaskQueue
     */
    protected $productTaskQueue;

    /**
     * @var ProcessedFeedFactory
     */
    protected $processedFeedFactory;

    /**
     * @var Config
     */
    protected $moduleConfig;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * CleanTask constructor
     *
     * @param ProductTaskQueueFactory $productTaskQueueFactory
     * @param ProcessedFeedFactory $processedFeedFactory
     * @param Config $moduleConfig
     * @param DateTime $dateTime
     * @param MadktingLogger $logger
     */
    public function __construct(
        ProductTaskQueueFactory $productTaskQueueFactory,
        ProcessedFeedFactory $processedFeedFactory,
        Config $moduleConfig,
        DateTime $dateTime,
        MadktingLogger $logger
    ) {
        $this->productTaskQueue = $productTaskQueueFactory->create();
        $this->processedFeedFactory = $processedFeedFactory;
        $this->moduleConfig = $moduleConfig;
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * Clean finished tasks
     *
     * @return array;
     */
    public function execute()
    {
        /* Deleted tasks success/error count */
        $tasksSuccess = 0;
        $tasksError = 0;

        /* Statuses time to clean */
        $currentTime = $this->dateTime->gmtTimestamp();
        $successLifetime = $this->moduleConfig->getTasksSuccessHistoryLifetime() * self::SECONDS_PER_DAY;
        $failureLifetime = $this->moduleConfig->getTasksFailureHistoryLifetime() * self::SECONDS_PER_DAY;

        /** @var ProductTaskQueue[] $finishedTasks */
        $finishedTasks = $this->productTaskQueue->getCollection()
            ->addFieldToFilter('status', ['in' => ProductTaskQueue::STATUS_COMPLETE . ',' . ProductTaskQueue::STATUS_ERROR]);

        foreach ($finishedTasks as $task) {
            try {
                $finishedTime = strtotime($task->getFinishedAt());

                if ($task->getStatus() == ProductTaskQueue::STATUS_COMPLETE && $currentTime > $finishedTime + $successLifetime
                    || $task->getStatus() == ProductTaskQueue::STATUS_ERROR && $currentTime > $finishedTime + $failureLifetime) {

                    /* If has feeds */
                    if (!empty($feedId = $task->getFeedId())) {
                        /** @var \Madkting\Connect\Model\ProcessedFeed $feed */
                        $feed = $this->processedFeedFactory->create()->load($feedId);

                        /* Delete feed if exists */
                        if (!empty($feed->getId())) {
                            $feed->delete();
                        }

                        /* Delete task */
                        $task->delete();
                        ++$tasksSuccess;
                    } else {

                        /* Delete task */
                        $task->delete();
                        ++$tasksSuccess;
                    }
                }
            } catch (\Exception $e) {
                ++$tasksError;
                $this->logger->error(__('Clean tasks error (%1), %2', $tasksError, $e->getMessage()));
            }
        }

        return [
            'success' => $tasksSuccess,
            'error' => $tasksError
        ];
    }
}
