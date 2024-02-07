<?php
namespace Mageplaza\Osc\Controller\Index\Index;

/**
 * Interceptor class for @see \Mageplaza\Osc\Controller\Index\Index
 */
class Interceptor extends \Mageplaza\Osc\Controller\Index\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Customer\Api\AccountManagementInterface $accountManagement, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\Translate\InlineInterface $translateInline, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Catalog\Model\ProductRepository $productRepository, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Checkout\Model\Cart $cart, \Psr\Log\LoggerInterface $logger, \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable, \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector, \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManagement, \Magento\Checkout\Model\Session $checkoutSession, \Mageplaza\Osc\Helper\Data $helper)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $customerRepository, $accountManagement, $coreRegistry, $translateInline, $formKeyValidator, $scopeConfig, $layoutFactory, $quoteRepository, $resultPageFactory, $resultLayoutFactory, $resultRawFactory, $resultJsonFactory, $productRepository, $storeManager, $cart, $logger, $configurable, $totalsCollector, $shippingMethodManagement, $checkoutSession, $helper);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        return $pluginInfo ? $this->___callPlugins('execute', func_get_args(), $pluginInfo) : parent::execute();
    }

    /**
     * {@inheritdoc}
     */
    public function initDefaultMethods(\Magento\Quote\Model\Quote $quote)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'initDefaultMethods');
        return $pluginInfo ? $this->___callPlugins('initDefaultMethods', func_get_args(), $pluginInfo) : parent::initDefaultMethods($quote);
    }

    /**
     * {@inheritdoc}
     */
    public function filterMethod($method)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'filterMethod');
        return $pluginInfo ? $this->___callPlugins('filterMethod', func_get_args(), $pluginInfo) : parent::filterMethod($method);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponCodeOsc($quote, $coupon)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCouponCodeOsc');
        return $pluginInfo ? $this->___callPlugins('setCouponCodeOsc', func_get_args(), $pluginInfo) : parent::setCouponCodeOsc($quote, $coupon);
    }

    /**
     * {@inheritdoc}
     */
    public function addProductOsc($skuArray)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addProductOsc');
        return $pluginInfo ? $this->___callPlugins('addProductOsc', func_get_args(), $pluginInfo) : parent::addProductOsc($skuArray);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        return $pluginInfo ? $this->___callPlugins('dispatch', func_get_args(), $pluginInfo) : parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getOnepage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getOnepage');
        return $pluginInfo ? $this->___callPlugins('getOnepage', func_get_args(), $pluginInfo) : parent::getOnepage();
    }

    /**
     * {@inheritdoc}
     */
    public function getActionFlag()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getActionFlag');
        return $pluginInfo ? $this->___callPlugins('getActionFlag', func_get_args(), $pluginInfo) : parent::getActionFlag();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        return $pluginInfo ? $this->___callPlugins('getRequest', func_get_args(), $pluginInfo) : parent::getRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResponse');
        return $pluginInfo ? $this->___callPlugins('getResponse', func_get_args(), $pluginInfo) : parent::getResponse();
    }
}
