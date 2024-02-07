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

namespace Madkting\Connect\Model\CategoriesMapping;

use Magento\Framework\Data\OptionSourceInterface;

class MadktingCategoriesOptions implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $categories;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return $this->getFormattedCategories();
    }

    /**
     * @return array
     */
    private function getFormattedCategories()
    {
        $this->getSearchableCategoriesArray();

        $formattedArray = [];

        foreach ($this->categories as $key => $value):
            $hasChild = false;
            foreach ($this->categories as $id => $category){
                if($key == $category['id_parent']){
                    $hasChild = true;
                }
            }

            if(!$hasChild){
                $formattedArray[] = [
                    'value' => $key,
                    'label' => $this->getParentsName($value['id_parent'], $value['name'])
                ];
            }
        endforeach;

        /* Order by parent */
        usort($formattedArray, function($a, $b){

            $order = strcasecmp($a['label'], $b['label']);

            if ($order < 0) return -1;
            elseif ($order > 0) return 1;
            else return 0;
        });

        return $formattedArray;
    }

    /**
     * @return array
     */
    public function getSearchableCategoriesArray()
    {
        if (empty($this->categories)) {
            $searchableArray = [];
            $categories = \Madkting\Categories();

            foreach ($categories as $category){
                $searchableArray[$category['id_category']] = [
                    'name' => $category['name'],
                    'id_parent' => $category['id_parent']
                ];
            }

            $this->categories = $searchableArray;
        }

        return $this->categories;
    }

    /**
     * @param int|null $parentId
     * @param string $label
     * @return string
     */
    public function getParentsName($parentId = null, $label = '')
    {
        if(is_null($parentId)){
            return $label;
        }else{
            return $this->getParentsName(
                $this->categories[$parentId]['id_parent'],
                $this->categories[$parentId]['name'] . ' > ' . $label
            );
        }
    }

    /**
     * @param array $categoriesArray
     * @return $this
     */
    public function setCategories($categoriesArray)
    {
        $this->categories = $categoriesArray;

        return $this;
    }
}
