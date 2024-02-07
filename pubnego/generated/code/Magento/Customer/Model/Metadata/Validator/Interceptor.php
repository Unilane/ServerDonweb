<?php
namespace Magento\Customer\Model\Metadata\Validator;

/**
 * Interceptor class for @see \Magento\Customer\Model\Metadata\Validator
 */
class Interceptor extends \Magento\Customer\Model\Metadata\Validator implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Model\Metadata\ElementFactory $attrDataFactory)
    {
        $this->___init();
        parent::__construct($attrDataFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($entityData)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isValid');
        return $pluginInfo ? $this->___callPlugins('isValid', func_get_args(), $pluginInfo) : parent::isValid($entityData);
    }

    /**
     * {@inheritdoc}
     */
    public function validateData(array $data, array $attributes, $entityType)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validateData');
        return $pluginInfo ? $this->___callPlugins('validateData', func_get_args(), $pluginInfo) : parent::validateData($data, $attributes, $entityType);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityType($entityType)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setEntityType');
        return $pluginInfo ? $this->___callPlugins('setEntityType', func_get_args(), $pluginInfo) : parent::setEntityType($entityType);
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
