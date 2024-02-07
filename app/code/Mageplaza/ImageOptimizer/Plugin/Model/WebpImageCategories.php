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
use Magento\Catalog\Model\Category\Image;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\ImageOptimizer\Helper\Data;

/**
 * Class WebpImageCategories
 * @package Mageplaza\ImageOptimizer\Plugin\Model
 */
class WebpImageCategories
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
     * WebpImageCategories constructor.
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
    public function afterGetUrl(Image $subject, $result)
    {
        $info = $this->helperData->getPathInfo($result);

        try {
            $storeId = $this->storeManager->getStore()->getId();
        } catch (Exception $e) {
            $storeId = null;
        }

        if ($this->helperData->isEnabled($storeId)
            && $this->helperData->isReplaceWebpImage() && isset($info['extension'])) {
            $newUrl  = $info['dirname'] . '/' . $info['filename'] . '.webp';
            if ($this->helperData->checkImageExists($newUrl)) {
                $result = $newUrl;
            }
        }

        return $result;
    }
}
