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
use Magento\Catalog\Model\View\Asset\Image;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ImageOptimizer\Helper\Data;

/**
 * Class WebpImageCatalog
 * @package Mageplaza\ImageOptimizer\Plugin\Model
 */
class WebpImageCatalog
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
     * WebpImageCatalog constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Data $helperData
    ) {
        $this->helperData   = $helperData;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Image $subject
     * @param string $result
     *
     * @return mixed
     */
    public function afterGetModule(Image $subject, $result)
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (Exception $e) {
            $storeId = null;
        }

        if ($this->helperData->isEnabled($storeId)
            && $this->helperData->isReplaceWebpImage()) {
            $result = 'mpiowebpcache';
        }

        return $result;
    }

    /**
     * @param Image $subject
     * @param string $result
     *
     * @return mixed
     */
    public function afterGetUrl(Image $subject, $result)
    {
        $info = $this->helperData->getPathInfo($result);
        $path = $subject->getPath();

        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (Exception $e) {
            $storeId = null;
        }

        if ($this->helperData->isEnabled($storeId)
            && $this->helperData->isReplaceWebpImage() && isset($info['extension'])) {
            $newUrl  = str_replace($info['extension'], 'webp', $result);
            $newPath = str_replace($info['extension'], 'webp', $path);
            $result  = str_replace('mpiowebpcache', 'cache', $result);
            if ($this->helperData->fileExists($newPath)) {
                $result = $newUrl;
            }
        }

        return $result;
    }
}
