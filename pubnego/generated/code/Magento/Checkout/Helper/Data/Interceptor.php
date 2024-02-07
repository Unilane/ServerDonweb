<?php
namespace Magento\Checkout\Helper\Data;

/**
 * Interceptor class for @see \Magento\Checkout\Helper\Data
 */
class Interceptor extends \Magento\Checkout\Helper\Data implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder, \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, ?\Magento\Sales\Api\PaymentFailuresInterface $paymentFailures = null)
    {
        $this->___init();
        parent::__construct($context, $storeManager, $checkoutSession, $localeDate, $transportBuilder, $inlineTranslation, $priceCurrency, $paymentFailures);
    }

    /**
     * {@inheritdoc}
     */
    public function getCheckout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCheckout');
        return $pluginInfo ? $this->___callPlugins('getCheckout', func_get_args(), $pluginInfo) : parent::getCheckout();
    }

    /**
     * {@inheritdoc}
     */
    public function getQuote()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQuote');
        return $pluginInfo ? $this->___callPlugins('getQuote', func_get_args(), $pluginInfo) : parent::getQuote();
    }

    /**
     * {@inheritdoc}
     */
    public function formatPrice($price)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'formatPrice');
        return $pluginInfo ? $this->___callPlugins('formatPrice', func_get_args(), $pluginInfo) : parent::formatPrice($price);
    }

    /**
     * {@inheritdoc}
     */
    public function convertPrice($price, $format = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertPrice');
        return $pluginInfo ? $this->___callPlugins('convertPrice', func_get_args(), $pluginInfo) : parent::convertPrice($price, $format);
    }

    /**
     * {@inheritdoc}
     */
    public function canOnepageCheckout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canOnepageCheckout');
        return $pluginInfo ? $this->___callPlugins('canOnepageCheckout', func_get_args(), $pluginInfo) : parent::canOnepageCheckout();
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceInclTax($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPriceInclTax');
        return $pluginInfo ? $this->___callPlugins('getPriceInclTax', func_get_args(), $pluginInfo) : parent::getPriceInclTax($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtotalInclTax($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSubtotalInclTax');
        return $pluginInfo ? $this->___callPlugins('getSubtotalInclTax', func_get_args(), $pluginInfo) : parent::getSubtotalInclTax($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePriceInclTax($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBasePriceInclTax');
        return $pluginInfo ? $this->___callPlugins('getBasePriceInclTax', func_get_args(), $pluginInfo) : parent::getBasePriceInclTax($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseSubtotalInclTax($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBaseSubtotalInclTax');
        return $pluginInfo ? $this->___callPlugins('getBaseSubtotalInclTax', func_get_args(), $pluginInfo) : parent::getBaseSubtotalInclTax($item);
    }

    /**
     * {@inheritdoc}
     */
    public function sendPaymentFailedEmail(\Magento\Quote\Model\Quote $checkout, string $message, string $checkoutType = 'onepage') : \Magento\Checkout\Helper\Data
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'sendPaymentFailedEmail');
        return $pluginInfo ? $this->___callPlugins('sendPaymentFailedEmail', func_get_args(), $pluginInfo) : parent::sendPaymentFailedEmail($checkout, $message, $checkoutType);
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedGuestCheckout(\Magento\Quote\Model\Quote $quote, $store = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAllowedGuestCheckout');
        return $pluginInfo ? $this->___callPlugins('isAllowedGuestCheckout', func_get_args(), $pluginInfo) : parent::isAllowedGuestCheckout($quote, $store);
    }

    /**
     * {@inheritdoc}
     */
    public function isContextCheckout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isContextCheckout');
        return $pluginInfo ? $this->___callPlugins('isContextCheckout', func_get_args(), $pluginInfo) : parent::isContextCheckout();
    }

    /**
     * {@inheritdoc}
     */
    public function isCustomerMustBeLogged()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isCustomerMustBeLogged');
        return $pluginInfo ? $this->___callPlugins('isCustomerMustBeLogged', func_get_args(), $pluginInfo) : parent::isCustomerMustBeLogged();
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayBillingOnPaymentMethodAvailable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isDisplayBillingOnPaymentMethodAvailable');
        return $pluginInfo ? $this->___callPlugins('isDisplayBillingOnPaymentMethodAvailable', func_get_args(), $pluginInfo) : parent::isDisplayBillingOnPaymentMethodAvailable();
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleOutputEnabled($moduleName = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isModuleOutputEnabled');
        return $pluginInfo ? $this->___callPlugins('isModuleOutputEnabled', func_get_args(), $pluginInfo) : parent::isModuleOutputEnabled($moduleName);
    }
}
