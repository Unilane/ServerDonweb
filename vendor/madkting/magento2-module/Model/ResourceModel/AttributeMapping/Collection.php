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

namespace Madkting\Connect\Model\ResourceModel\AttributeMapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Madkting\Connect\Model\ResourceModel\AttributeMapping
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'attribute_mapping_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'madkting_attribute_mapping_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'attribute_mapping_collection';

    /**
     * Collection constructor
     */
    protected function _construct()
    {
        $this->_init('Madkting\Connect\Model\AttributeMapping', 'Madkting\Connect\Model\ResourceModel\AttributeMapping');
    }

    /**
     * Return attribute match data
     *
     * @param int|null $madktingId
     * @return array
     */
    public function getSelectableAttributes($madktingId = null)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            ['mapping' => $this->getMainTable()],
            ['magento_id' => 'magento_attribute_id', 'madkting_id' => 'madkting_attribute_id']
        )->join(
            ['madkting' => $this->getResource()->getTable('madkting_attribute')],
            'madkting.attribute_id = mapping.madkting_attribute_id',
            ['madkting_label' => 'attribute_label']
        )->join(
            ['magento' => $this->getResource()->getTable('eav_attribute')],
            'magento.attribute_id = mapping.magento_attribute_id',
            ['magento_label' => 'frontend_label']
        )->where(
            'madkting.attribute_format = ?',
            'select'
        )->where(
            'magento.frontend_input = ?',
            'select'
        )->group('CONCAT(magento_id, "-", madkting_id)')
        ->order('mapping.attribute_mapping_id');

        if (!empty($madktingId)) {
            $select->where(
                'madkting.attribute_id = ?',
                $madktingId
            );
        }

        return $connection->fetchAll($select);
    }

    /**
     * @inheritdoc
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }

    /**
     * @inheritdoc
     */
    protected function _toOptionArray($valueField = 'attribute_mapping_id', $labelField = 'attribute_id', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
