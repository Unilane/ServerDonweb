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

namespace Madkting\Connect\Model\Config\Source;

use Madkting\Connect\Model\Config as MadktingConfig;
use Madkting\Connect\Model\StockRepositoryFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Option\ArrayInterface;

class InventoryStocks implements ArrayInterface
{
    /**
     * @var mixed
     */
    protected $stockRepository;

    /**
     * @var MadktingConfig
     */
    protected $madktingConfig;

    /**
     * InventoryStocks constructor
     *
     * @param MadktingConfig $madktingConfig
     */
    public function __construct(
        StockRepositoryFactory $stockRepository,
        MadktingConfig $madktingConfig
    ) {
        $this->stockRepository = $stockRepository->create();
        $this->madktingConfig = $madktingConfig;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $magentoVersion = $this->madktingConfig->getMagentoVersion();
        if (version_compare($magentoVersion, '2.3', '<')) {
            return [
                [
                    'value' => '',
                    'label' => __('Option valid only for Magento 2.3+')
                ]
            ];
        } else {
            $options = [];
            $stocks = $this->stockRepository->getList()->getItems();
            foreach ($stocks as $stock) {
                $options[] = ['value' => $stock->getName(), 'label' => $stock->getName()];
            }
            return $options;
        }
    }
}
