<?php
namespace Mageplaza\SocialLogin\Controller\Social\Login;

/**
 * Interceptor class for @see \Mageplaza\SocialLogin\Controller\Social\Login
 */
class Interceptor extends \Mageplaza\SocialLogin\Controller\Social\Login implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Api\AccountManagementInterface $accountManager, \Mageplaza\SocialLogin\Helper\Social $apiHelper, \Mageplaza\SocialLogin\Model\Social $apiObject, \Magento\Customer\Model\Session $customerSession, \Magento\Customer\Model\Account\Redirect $accountRedirect, \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Customer\Model\Customer $customerModel)
    {
        $this->___init();
        parent::__construct($context, $storeManager, $accountManager, $apiHelper, $apiObject, $customerSession, $accountRedirect, $resultRawFactory, $customerModel);
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
    public function getStore()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStore');
        return $pluginInfo ? $this->___callPlugins('getStore', func_get_args(), $pluginInfo) : parent::getStore();
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomerProcess($userProfile, $type)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createCustomerProcess');
        return $pluginInfo ? $this->___callPlugins('createCustomerProcess', func_get_args(), $pluginInfo) : parent::createCustomerProcess($userProfile, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomer($user, $type)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'createCustomer');
        return $pluginInfo ? $this->___callPlugins('createCustomer', func_get_args(), $pluginInfo) : parent::createCustomer($user, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function emailRedirect($apiLabel, $needTranslate = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'emailRedirect');
        return $pluginInfo ? $this->___callPlugins('emailRedirect', func_get_args(), $pluginInfo) : parent::emailRedirect($apiLabel, $needTranslate);
    }

    /**
     * {@inheritdoc}
     */
    public function _appendJs($content = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '_appendJs');
        return $pluginInfo ? $this->___callPlugins('_appendJs', func_get_args(), $pluginInfo) : parent::_appendJs($content);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh($customer)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'refresh');
        return $pluginInfo ? $this->___callPlugins('refresh', func_get_args(), $pluginInfo) : parent::refresh($customer);
    }

    /**
     * {@inheritdoc}
     */
    public function login($type)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'login');
        return $pluginInfo ? $this->___callPlugins('login', func_get_args(), $pluginInfo) : parent::login($type);
    }

    /**
     * {@inheritdoc}
     */
    public function checkRequest($key, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkRequest');
        return $pluginInfo ? $this->___callPlugins('checkRequest', func_get_args(), $pluginInfo) : parent::checkRequest($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCustomerLogin()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'checkCustomerLogin');
        return $pluginInfo ? $this->___callPlugins('checkCustomerLogin', func_get_args(), $pluginInfo) : parent::checkCustomerLogin();
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
