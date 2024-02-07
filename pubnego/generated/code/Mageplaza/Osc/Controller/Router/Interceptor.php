<?php
namespace Mageplaza\Osc\Controller\Router;

/**
 * Interceptor class for @see \Mageplaza\Osc\Controller\Router
 */
class Interceptor extends \Mageplaza\Osc\Controller\Router implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ActionFactory $actionFactory, \Mageplaza\Osc\Helper\Data $helperData)
    {
        $this->___init();
        parent::__construct($actionFactory, $helperData);
    }

    /**
     * {@inheritdoc}
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'match');
        return $pluginInfo ? $this->___callPlugins('match', func_get_args(), $pluginInfo) : parent::match($request);
    }
}
