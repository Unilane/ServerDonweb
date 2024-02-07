<?php
namespace Magento\Eav\Model\Validator\Attribute\Data;

/**
 * Interceptor class for @see \Magento\Eav\Model\Validator\Attribute\Data
 */
class Interceptor extends \Magento\Eav\Model\Validator\Attribute\Data implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Eav\Model\AttributeDataFactory $attrDataFactory, array $ignoredAttributesByTypesList = [])
    {
        $this->___init();
        parent::__construct($attrDataFactory, $ignoredAttributesByTypesList);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setAttributes');
        return $pluginInfo ? $this->___callPlugins('setAttributes', func_get_args(), $pluginInfo) : parent::setAttributes($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowedAttributesList(array $attributesCodes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setAllowedAttributesList');
        return $pluginInfo ? $this->___callPlugins('setAllowedAttributesList', func_get_args(), $pluginInfo) : parent::setAllowedAttributesList($attributesCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function setDeniedAttributesList(array $attributesCodes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDeniedAttributesList');
        return $pluginInfo ? $this->___callPlugins('setDeniedAttributesList', func_get_args(), $pluginInfo) : parent::setDeniedAttributesList($attributesCodes);
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setData');
        return $pluginInfo ? $this->___callPlugins('setData', func_get_args(), $pluginInfo) : parent::setData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($entity)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isValid');
        return $pluginInfo ? $this->___callPlugins('isValid', func_get_args(), $pluginInfo) : parent::isValid($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslator(?\Laminas\Validator\Translator\TranslatorInterface $translator = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTranslator');
        return $pluginInfo ? $this->___callPlugins('setTranslator', func_get_args(), $pluginInfo) : parent::setTranslator($translator);
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslator()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTranslator');
        return $pluginInfo ? $this->___callPlugins('getTranslator', func_get_args(), $pluginInfo) : parent::getTranslator();
    }

    /**
     * {@inheritdoc}
     */
    public function hasTranslator()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'hasTranslator');
        return $pluginInfo ? $this->___callPlugins('hasTranslator', func_get_args(), $pluginInfo) : parent::hasTranslator();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMessages');
        return $pluginInfo ? $this->___callPlugins('getMessages', func_get_args(), $pluginInfo) : parent::getMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessages()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'hasMessages');
        return $pluginInfo ? $this->___callPlugins('hasMessages', func_get_args(), $pluginInfo) : parent::hasMessages();
    }
}
