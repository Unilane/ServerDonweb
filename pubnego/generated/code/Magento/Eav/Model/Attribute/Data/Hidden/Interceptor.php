<?php
namespace Magento\Eav\Model\Attribute\Data\Hidden;

/**
 * Interceptor class for @see \Magento\Eav\Model\Attribute\Data\Hidden
 */
class Interceptor extends \Magento\Eav\Model\Attribute\Data\Hidden implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Framework\Stdlib\StringUtils $stringHelper)
    {
        $this->___init();
        parent::__construct($localeDate, $logger, $localeResolver, $stringHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function extractValue(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'extractValue');
        return $pluginInfo ? $this->___callPlugins('extractValue', func_get_args(), $pluginInfo) : parent::extractValue($request);
    }

    /**
     * {@inheritdoc}
     */
    public function validateValue($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validateValue');
        return $pluginInfo ? $this->___callPlugins('validateValue', func_get_args(), $pluginInfo) : parent::validateValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function compactValue($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'compactValue');
        return $pluginInfo ? $this->___callPlugins('compactValue', func_get_args(), $pluginInfo) : parent::compactValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function restoreValue($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'restoreValue');
        return $pluginInfo ? $this->___callPlugins('restoreValue', func_get_args(), $pluginInfo) : parent::restoreValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function outputValue($format = 'text')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'outputValue');
        return $pluginInfo ? $this->___callPlugins('outputValue', func_get_args(), $pluginInfo) : parent::outputValue($format);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setAttribute');
        return $pluginInfo ? $this->___callPlugins('setAttribute', func_get_args(), $pluginInfo) : parent::setAttribute($attribute);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAttribute');
        return $pluginInfo ? $this->___callPlugins('getAttribute', func_get_args(), $pluginInfo) : parent::getAttribute();
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestScope($scope)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setRequestScope');
        return $pluginInfo ? $this->___callPlugins('setRequestScope', func_get_args(), $pluginInfo) : parent::setRequestScope($scope);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestScopeOnly($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setRequestScopeOnly');
        return $pluginInfo ? $this->___callPlugins('setRequestScopeOnly', func_get_args(), $pluginInfo) : parent::setRequestScopeOnly($flag);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntity(\Magento\Framework\Model\AbstractModel $entity)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setEntity');
        return $pluginInfo ? $this->___callPlugins('setEntity', func_get_args(), $pluginInfo) : parent::setEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEntity');
        return $pluginInfo ? $this->___callPlugins('getEntity', func_get_args(), $pluginInfo) : parent::getEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtractedData(array $data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setExtractedData');
        return $pluginInfo ? $this->___callPlugins('setExtractedData', func_get_args(), $pluginInfo) : parent::setExtractedData($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtractedData($index = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExtractedData');
        return $pluginInfo ? $this->___callPlugins('getExtractedData', func_get_args(), $pluginInfo) : parent::getExtractedData($index);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAjaxRequest($flag = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setIsAjaxRequest');
        return $pluginInfo ? $this->___callPlugins('setIsAjaxRequest', func_get_args(), $pluginInfo) : parent::setIsAjaxRequest($flag);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAjaxRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsAjaxRequest');
        return $pluginInfo ? $this->___callPlugins('getIsAjaxRequest', func_get_args(), $pluginInfo) : parent::getIsAjaxRequest();
    }
}
