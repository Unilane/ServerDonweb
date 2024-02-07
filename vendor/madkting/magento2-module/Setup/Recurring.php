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

namespace Madkting\Connect\Setup;

use Madkting\MadktingClient;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class Recurring
 * @package Madkting\Connect\Setup
 */
class Recurring implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        /* Update Madkting attributes with SDK */
        $fields = MadktingClient::getProductFieldsDictionary();

        /* Update fields */
        $attributes = [];
        $options = [];
        foreach ($fields as $field) {
            $hasOptions = !empty($field['options']);
            $attributes[] = [
                'attribute_id' => !empty($field['id_product_field']) ? $field['id_product_field'] : null,
                'attribute_code' => !empty($field['name']) ? $field['name'] : null,
                'attribute_label' => !empty($field['text']) ? $field['text'] : null,
                'attribute_format' => !empty($field['format']) ? $field['format'] : null,
                'max_length' => !empty($field['max_length']) ? $field['max_length'] : null,
                'min_num' => !empty($field['min_num']) ? $field['min_num'] : null,
                'max_num' => !empty($field['max_num']) ? $field['max_num'] : null,
                'default_value' => !empty($field['default']) ? $field['default'] : null,
                'requirement' => !empty($field['requirement']) ? $field['requirement'] : null,
                'in_variation' => !empty($field['in_variation']) ? $field['in_variation'] : null,
                'has_options' => $hasOptions,
                'tooltip' => !empty($field['tooltip']) ? $field['tooltip'] : null,
                'sort_order' => !empty($field['position']) ? $field['position'] : null
            ];

            if ($hasOptions) {
                foreach ($field['options'] as $key => $option) {
                    $id = strtolower(preg_replace('/\s/', '_',$field['name'].'_'.$option['value']));
                    $options[] = [
                        'attribute_option_id' => $id,
                        'attribute_id' => $field['id_product_field'],
                        'option_value' => $option['value'],
                        'option_label' => $option['text'],
                        'sort_order' => $key
                    ];
                }
            }
        }
        if (!empty($attributes)) {
            $attributeTable = $setup->getTable('madkting_attribute');
            $connection->insertOnDuplicate($attributeTable, $attributes);
        }
        if (!empty($options)) {
            $attributeTable = $setup->getTable('madkting_attribute_option');
            $connection->insertOnDuplicate($attributeTable, $options);
        }

        $setup->endSetup();
    }
}
