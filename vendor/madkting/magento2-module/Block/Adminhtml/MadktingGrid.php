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

namespace Madkting\Connect\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;

/**
 * Class MadktingGrid
 * @package Madkting\Connect\Block\Adminhtml
 */
class MadktingGrid extends Container
{
    /**
     * @var string
     */
    protected $gridName = 'madkting';

    /**
     * @var string
     */
    protected $grid = '';

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @var bool
     */
    protected $isForm = false;

    /**
     * @var string
     */
    protected $formPath = '';

    /**
     * Get grid's name
     *
     * @return string
     */
    public function getGridName()
    {
        return $this->gridName;
    }

    /**
     * Get form action path
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl($this->formPath);
    }

    /**
     * Add column
     * @param $columnCode
     * @param $columnName
     */
    protected function addColumn($columnCode, $columnName)
    {
        $this->columns[$columnCode] = $columnName;
    }

    /**
     * Add table row
     *
     * @param array $fields
     * @throws \Exception
     */
    protected function addRow($fields)
    {
        if (!is_array($fields)) {
            throw new \Exception('Row data must be an array');
        }

        $rowFields = [];
        foreach ($fields as $field) {
            $rowFields[$field['columnCode']] = [
                'value' => $field['value'],
                'type' => !empty($field['type']) ? $field['type'] : '',
                'class' => !empty($field['class']) ? $field['class'] : ''
            ];
        }

        $this->rows[] = $rowFields;
    }

    /**
     * Get table
     *
     * @return string
     */
    public function getGrid()
    {
        /* If is form add open tag */
        if($this->isForm) {
            $this->grid = '<form id="' . $this->gridName . '-form" method="POST" action="' . $this->getFormAction() . '">';
        }

        /* Start table */
        $this->grid .= '<table id="' . $this->gridName . '-table" class="data-grid">';

        /* Add columns */
        if (!empty($this->columns)) {

            /* Add columns title */
            $this->grid .= '<thead><tr>';
            foreach ($this->columns as $title) {
                $this->grid .= '<th class="data-grid-th"><span class="data-grid-cell-content">' . $title . '</span></th>';
            }
            $this->grid .= '</tr></thead><tbody>';

            if (!empty($this->rows)) {

                /* Add rows values */
                $rowNumber = 1;
                foreach ($this->rows as $row) {
                    $rowClass = $rowNumber%2 == 0 ? 'data-row _odd-row' : 'data-row';
                    ++$rowNumber;
                    $this->grid .= '<tr class="' . $rowClass . '">';

                    foreach ($this->columns as $code => $name) {

                        /* Set value */
                        $data = $row[$code];
                        if (!empty($data['type'])) {
                            switch ($data['type']) {
                                case 'span':
                                    $value = '<span>' . $data['value'] . '</span>';
                                    break;
                                case 'text':
                                    $value = '<input class="input-text admin__control-text" type="text" value="' . $data['value'] . '" />';
                                    break;
                                case 'select':

                                    /* Get options */
                                    $options = '';
                                    if (is_array($data['value'])) {
                                        foreach ($data['value'] as $option) {
                                            $selected = !empty($option['selected']) ? 'selected' : '';
                                            $options .= '<option value="' . $option['value'] . '" ' . $selected . '>' . $option['label'] . '</option>';
                                        }
                                    } else {
                                        $options .= '<option value="0">' . $data['value'] . '</option>';
                                    }

                                    $value = '<select class="admin__control-select">' . $options . '</select>';
                                    break;
                                default:
                                    $value = $data['value'];
                            }
                        } else {
                            $value = $data['value'];
                        }

                        $fieldClass = !empty($data['class']) ? $data['class'] . ' align-middle' : 'align-middle';
                        $this->grid .= '<td class="' . $fieldClass . '"><div class="data-grid-cell-content">' . $value . '</div></td>';
                    }

                    $this->grid .= '</tr>';
                }
            } else {
                $colspan = '';
                if (!empty($this->columns)) {
                    $colspan = 'colspan="' . count($this->columns) . '"';
                }
                $this->grid .= '<tr class="data-grid-tr-no-data"><td ' . $colspan . '>' . __('We couldn\'t find any records.') . '</td></tr>';
            }

            $this->grid .= '</tbody>';
        }

        $this->grid .= '</table>';

        /* If is form add close tag */
        if($this->isForm) {
            $this->grid .= '</form>';
        }

        return $this->grid;
    }
}
