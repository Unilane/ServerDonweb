<?php
namespace Magento\Framework\View\Page\Config\Structure;

/**
 * Interceptor class for @see \Magento\Framework\View\Page\Config\Structure
 */
class Interceptor extends \Magento\Framework\View\Page\Config\Structure implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function setElementAttribute($element, $attributeName, $attributeValue)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setElementAttribute');
        return $pluginInfo ? $this->___callPlugins('setElementAttribute', func_get_args(), $pluginInfo) : parent::setElementAttribute($element, $attributeName, $attributeValue);
    }

    /**
     * {@inheritdoc}
     */
    public function processRemoveElementAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'processRemoveElementAttributes');
        return $pluginInfo ? $this->___callPlugins('processRemoveElementAttributes', func_get_args(), $pluginInfo) : parent::processRemoveElementAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyClass($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setBodyClass');
        return $pluginInfo ? $this->___callPlugins('setBodyClass', func_get_args(), $pluginInfo) : parent::setBodyClass($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyClasses()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBodyClasses');
        return $pluginInfo ? $this->___callPlugins('getBodyClasses', func_get_args(), $pluginInfo) : parent::getBodyClasses();
    }

    /**
     * {@inheritdoc}
     */
    public function getElementAttributes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getElementAttributes');
        return $pluginInfo ? $this->___callPlugins('getElementAttributes', func_get_args(), $pluginInfo) : parent::getElementAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTitle');
        return $pluginInfo ? $this->___callPlugins('setTitle', func_get_args(), $pluginInfo) : parent::setTitle($title);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTitle');
        return $pluginInfo ? $this->___callPlugins('getTitle', func_get_args(), $pluginInfo) : parent::getTitle();
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata($name, $content)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setMetadata');
        return $pluginInfo ? $this->___callPlugins('setMetadata', func_get_args(), $pluginInfo) : parent::setMetadata($name, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMetadata');
        return $pluginInfo ? $this->___callPlugins('getMetadata', func_get_args(), $pluginInfo) : parent::getMetadata();
    }

    /**
     * {@inheritdoc}
     */
    public function addAssets($name, $attributes)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addAssets');
        return $pluginInfo ? $this->___callPlugins('addAssets', func_get_args(), $pluginInfo) : parent::addAssets($name, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAssets($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeAssets');
        return $pluginInfo ? $this->___callPlugins('removeAssets', func_get_args(), $pluginInfo) : parent::removeAssets($name);
    }

    /**
     * {@inheritdoc}
     */
    public function processRemoveAssets()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'processRemoveAssets');
        return $pluginInfo ? $this->___callPlugins('processRemoveAssets', func_get_args(), $pluginInfo) : parent::processRemoveAssets();
    }

    /**
     * {@inheritdoc}
     */
    public function getAssets()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAssets');
        return $pluginInfo ? $this->___callPlugins('getAssets', func_get_args(), $pluginInfo) : parent::getAssets();
    }

    /**
     * {@inheritdoc}
     */
    public function __toArray()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__toArray');
        return $pluginInfo ? $this->___callPlugins('__toArray', func_get_args(), $pluginInfo) : parent::__toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function populateWithArray(array $data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'populateWithArray');
        return $pluginInfo ? $this->___callPlugins('populateWithArray', func_get_args(), $pluginInfo) : parent::populateWithArray($data);
    }
}
