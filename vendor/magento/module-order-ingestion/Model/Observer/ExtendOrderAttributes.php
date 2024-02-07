<?php

namespace Magento\OrderIngestion\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class ExtendOrderAttributes implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();

        /* @var $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getEvent()->getQuote();

        $order->setExtOrderId($quote->getExtOrderId());
    }
}
