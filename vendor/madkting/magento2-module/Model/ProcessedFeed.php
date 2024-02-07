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
 * @author Carlos Guillermo Jiménez Salcedo <guillermo@madkting.com>
 * @link https://bitbucket.org/madkting/magento2
 * @copyright Copyright (c) 2017 Madkting Software.
 * @license See LICENSE.txt for license details.
 */

namespace Madkting\Connect\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ProcessedFeed
 * @package Madkting\Connect\Model
 *
 * @method CategoriesMapping setEvent(\string $event)
 * @method CategoriesMapping setLocation(\string $location)
 * @method CategoriesMapping setStatus(\string $status)
 * @method CategoriesMapping setResult(\string $result)
 * @method CategoriesMapping setSuccessCount(\int $successCount)
 * @method CategoriesMapping setErrorCount(\int $errorCount)
 * @method CategoriesMapping setCriticalCount(\int $criticalCount)
 * @method CategoriesMapping setReceivedCount(\int $receivedCount)
 * @method CategoriesMapping setCreatedAt(\string $createdAt)
 * @method CategoriesMapping setUpdatedAt(\string $updatedAt)
 * @method string getEvent()
 * @method string getLocation()
 * @method string getStatus()
 * @method string getResult()
 * @method int getSuccessCount()
 * @method int getErrorCount()
 * @method int getCriticalCount()
 * @method int getReceivedCount()
 * @method string getCreatedAt()
 * @method string getUpdatedAt()
 */
class ProcessedFeed extends AbstractModel implements IdentityInterface
{
    /**
     * Cache Tag
     */
    const CACHE_TAG = "madkting_processed_feed";

    /**
     * Feed events
     */
    const EVENT_ORDER = 'product_feed:order:created';
    const EVENT_PRODUCT = 'product_feed:feed:finish';

    protected $_eventPrefix = "madkting_processed_feed";

    public function _construct()
    {
        $this->_init('Madkting\Connect\Model\ResourceModel\ProcessedFeed');
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
}
