<?php
namespace Magento\Customer\Model\Address\CustomAttributeList;

/**
 * Interceptor class for @see \Magento\Customer\Model\Address\CustomAttributeList
 */
class Interceptor extends \Magento\Customer\Model\Address\CustomAttributeList implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttributes');
        return $pluginInfo ? $this->___callPlugins('getAttributes', func_get_args(), $pluginInfo) : parent::getAttributes();
    }
}
