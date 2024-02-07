<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\TotalsItemInterface
 */
class TotalsItemExtension extends \Magento\Framework\Api\AbstractSimpleObject implements TotalsItemExtensionInterface
{
    /**
     * @return string|null
     */
    public function getMposc()
    {
        return $this->_get('mposc');
    }

    /**
     * @param string $mposc
     * @return $this
     */
    public function setMposc($mposc)
    {
        $this->setData('mposc', $mposc);
        return $this;
    }
}
