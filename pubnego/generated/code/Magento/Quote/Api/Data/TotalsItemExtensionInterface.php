<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Quote\Api\Data\TotalsItemInterface
 */
interface TotalsItemExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return string|null
     */
    public function getMposc();

    /**
     * @param string $mposc
     * @return $this
     */
    public function setMposc($mposc);
}
