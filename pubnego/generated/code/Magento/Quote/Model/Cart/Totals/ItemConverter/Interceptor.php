<?php
namespace Magento\Quote\Model\Cart\Totals\ItemConverter;

/**
 * Interceptor class for @see \Magento\Quote\Model\Cart\Totals\ItemConverter
 */
class Interceptor extends \Magento\Quote\Model\Cart\Totals\ItemConverter implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Quote\Api\Data\TotalsItemInterfaceFactory $totalsItemFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, ?\Magento\Framework\Serialize\Serializer\Json $serializer = null)
    {
        $this->___init();
        parent::__construct($configurationPool, $eventManager, $totalsItemFactory, $dataObjectHelper, $serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function modelToDataObject($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'modelToDataObject');
        return $pluginInfo ? $this->___callPlugins('modelToDataObject', func_get_args(), $pluginInfo) : parent::modelToDataObject($item);
    }
}
