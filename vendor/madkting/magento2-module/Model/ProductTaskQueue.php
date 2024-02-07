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

namespace Madkting\Connect\Model;

use Madkting\Connect\Logger\MadktingLogger;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class ProductTaskQueue
 * @package Madkting\Connect\Model
 *
 * @method ProductTaskQueue setProductId(\int $productId)
 * @method ProductTaskQueue setTaskType(\int $taskType)
 * @method ProductTaskQueue setStatus(\int $status)
 * @method ProductTaskQueue setAction(\int $action)
 * @method ProductTaskQueue setBeforeAction(\int $beforeAction)
 * @method ProductTaskQueue setAfterAction(\int $afterAction)
 * @method ProductTaskQueue setSelectiveSync(\string $selectiveSync)
 * @method ProductTaskQueue setMadktingAttributes(\string $madktingAttribues)
 * @method ProductTaskQueue setFeedId(\string $feedId)
 * @method ProductTaskQueue setFeedPosition(\int $feedPosition)
 * @method ProductTaskQueue setCreatedAt(\string $createdAt)
 * @method ProductTaskQueue setStartedAt(\string $startedAt)
 * @method ProductTaskQueue setFinishedAt(\string $finishedAt)
 * @method int getProductId()
 * @method int getTaskType()
 * @method int getStatus()
 * @method int getAction()
 * @method int getBeforeAction()
 * @method int getAfterAction()
 * @method string getSelectiveSync()
 * @method string getMadktingAttributes()
 * @method string getFeedId()
 * @method int getFeedPosition()
 * @method string getCreatedAt()
 * @method string getStartedAt()
 * @method string getFinishedAt()
 */
class ProductTaskQueue extends AbstractModel implements IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'madkting_product_task_queue';

    /**
     * Task type
     */
    const TYPE_PRODUCT = 1;
    const TYPE_VARIATION = 2;
    const TYPE_IMAGE = 3;

    /**
     * Task statuses
     */
    const STATUS_WAITING = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_COMPLETE = 3;
    const STATUS_ERROR = 4;

    /**
     * Task actions
     */
    const ACTION_NONE = 0;
    const ACTION_CREATE = 1;
    const ACTION_UPDATE = 2;
    const ACTION_DELETE = 3;

    /**
     * @var string
     */
    protected $_cacheTag = 'madkting_product_task_queue';

    /**
     * @var string
     */
    protected $_eventPrefix = 'madkting_product_task_queue';

    /**
     * Status labels
     */
    protected $statuses = [
        self::STATUS_WAITING => 'Waiting',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETE => 'Complete'
    ];

    /**
     * Status labels
     */
    protected $actions = [
        self::ACTION_NONE => 'None',
        self::ACTION_CREATE => 'Create',
        self::ACTION_UPDATE => 'Update',
        self::ACTION_DELETE => 'Delete'
    ];

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var MadktingLogger
     */
    protected $logger;

    /**
     * ProductTaskQueue constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param MadktingLogger $logger
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        MadktingLogger $logger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->dateTime = $dateTime;
        $this->logger = $logger;
    }

    /**
     * ProductTaskQueue construct
     */
    protected function _construct()
    {
        $this->_init('Madkting\Connect\Model\ResourceModel\ProductTaskQueue');
    }

    /**
     * Get status label by id
     *
     * @param int $id
     * @return string|bool
     */
    public function getStatusById($id)
    {
        return array_key_exists($id, $this->statuses) ? __($this->statuses[$id]) : false;
    }

    /**
     * Get action label by id
     *
     * @param int $id
     * @return string|bool
     */
    public function getActionById($id)
    {
        return array_key_exists($id, $this->actions) ? __($this->actions[$id]) : false;
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }

    /**
     * Processing object before save data
     *
     * @return $this
     * @throws InputException
     */
    public function beforeSave()
    {
        if (!$this->getId()) {
            if ($this->getTaskType() != self::TYPE_IMAGE) {

                /* Task is not image type */
                $exists = $this->getCollection()
                    ->addFieldToFilter('product_id', $this->getProductId())
                    ->addFieldToFilter('status', ['nin' => [self::STATUS_COMPLETE, self::STATUS_ERROR]])
                    ->addFieldToFilter('task_type', ['neq' => self::TYPE_IMAGE])
                    ->getLastItem()->getData();

                if (!empty($exists)) {
                    if ($exists['action'] != self::ACTION_UPDATE) {
                        $message = __('Product %1 is already queued (%2)', $this->getProductId(), $this->actions[$this->getAction()]);
                        $this->logger->info($message);
                        throw new InputException($message);
                    }
                }
            } else {

                /* Task is image type */
                $exists = $this->getCollection()
                    ->addFieldToFilter('product_id', $this->getProductId())
                    ->addFieldToFilter('status', ['nin' => [self::STATUS_COMPLETE, self::STATUS_ERROR]])
                    ->addFieldToFilter('task_type', self::TYPE_IMAGE)
                    ->addFieldToFilter('action', $this->getAction())
                    ->getLastItem()->getData();

                if (!empty($exists)) {
                    $message = __('Product %1 image is already queued (%2)', $this->getProductId(), $this->actions[$this->getAction()]);
                    $this->logger->info($message);
                    throw new InputException($message);
                }
            }
        }

        return parent::beforeSave();
    }

    /**
     * @return $this
     * @throws \Throwable|InputException
     */
    public function save()
    {
        try {
            return parent::save();
        } catch (InputException $e) {
            throw $e;
        } catch (\Exception $e) {
            $message = __('Product %1 queue error = %2', $this->getProductId(), $e->getMessage());
            $this->logger->exception($e, $message);
            throw $e;
        } catch (\Throwable $t) {
            $message = __('Product %1 queue error = %2', $this->getProductId(), $t->getMessage());
            $this->logger->exception($t, $message);
            throw $t;
        }
    }

    /**
     * Start task
     *
     * @return $this
     */
    public function startTask()
    {
        $this->setStatus(self::STATUS_PROCESSING)
            ->setStartedAt(date("M d Y H:i:s", $this->dateTime->gmtTimestamp()))
            ->save();

        return $this;
    }

    /**
     * Finish task
     *
     * @param int $status
     * @return $this
     */
    public function finishTask($status = self::STATUS_COMPLETE)
    {
        $this->setStatus($status)
            ->setFinishedAt(date("M d Y H:i:s", $this->dateTime->gmtTimestamp()))
            ->save();

        return $this;
    }
}
