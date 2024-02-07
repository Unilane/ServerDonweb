<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ImageOptimizer
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ImageOptimizer\Plugin\Model;

use Exception;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Model\Template\Filter;
use Mageplaza\ImageOptimizer\Helper\Data;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class WebpImageWidget
 * @package Mageplaza\ImageOptimizer\Plugin\Model
 */
class WebpImageWidget
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DirectoryList
     */
    protected $dir;

    /**
     * WebpImageCategories constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Data $helperData,
        DirectoryList $dir
    ) {
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
        $this->dir          = $dir;
    }

    /**
     * @param Filter $subject
     * @param string $result
     *
     * @return string
     */
    public function afterMediaDirective(Filter $subject, $result)
    {
        $info = $this->helperData->getPathInfo($result);
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (Exception $e) {
            $storeId = null;
        }
        $newUrl       = $info['dirname'] . '/' . $info['filename'] . '.webp';
        $absolutePath = $this->dir->getRoot() . stristr($info['dirname'], "/pub") . '/' . $info['filename'] . '.webp';
        if ($this->helperData->isEnabled($storeId)
            && $this->helperData->isReplaceWebpImage()
            && isset($info['extension'])
            && file_exists($absolutePath)) {
            if ($this->helperData->checkImageExists($newUrl)) {
                $result = $newUrl;
            }
        }

        return $result;
    }
}
