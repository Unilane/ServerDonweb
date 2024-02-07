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

use Madkting\Connect\Model\Config as MadktingConfig;
use Magento\Framework\ObjectManagerInterface;

/**
 * Factory class for @see \Madkting\Connect\Model\StockRepository
 * @package Madkting\Connect\Model
 */
class StockRepositoryFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var MadktingConfig
     */
    protected $madktingConfig;

    /**
     * SalableStockFactory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param MadktingConfig $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        MadktingConfig $config
    ) {
        $this->objectManager = $objectManager;
        $this->madktingConfig = $config;
    }

    /**
     * Return stock repository according to Magento's version
     *
     * @return mixed
     */
    public function create()
    {
        $magentoVersion = $this->madktingConfig->getMagentoVersion();
        if (version_compare($magentoVersion, '2.3', '>')) {
            return $this->objectManager->create('\\Magento\\InventoryApi\\Api\\StockRepositoryInterface');
        } else {
            return null;
        }
    }
}
