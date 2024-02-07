<?php
namespace Magento\Customer\Model\AttributeMetadataDataProvider;

/**
 * Interceptor class for @see \Magento\Customer\Model\AttributeMetadataDataProvider
 */
class Interceptor extends \Magento\Customer\Model\AttributeMetadataDataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Eav\Model\Config $eavConfig, \Magento\Customer\Model\ResourceModel\Form\Attribute\CollectionFactory $attrFormCollectionFactory, \Magento\Store\Model\StoreManager $storeManager)
    {
        $this->___init();
        parent::__construct($eavConfig, $attrFormCollectionFactory, $storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($entityType, $attributeCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttribute');
        return $pluginInfo ? $this->___callPlugins('getAttribute', func_get_args(), $pluginInfo) : parent::getAttribute($entityType, $attributeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributeCodes($entityType, $attributeSetId = 0, $storeId = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAllAttributeCodes');
        return $pluginInfo ? $this->___callPlugins('getAllAttributeCodes', func_get_args(), $pluginInfo) : parent::getAllAttributeCodes($entityType, $attributeSetId, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributesCollection($entityType, $formCode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'loadAttributesCollection');
        return $pluginInfo ? $this->___callPlugins('loadAttributesCollection', func_get_args(), $pluginInfo) : parent::loadAttributesCollection($entityType, $formCode);
    }
}
