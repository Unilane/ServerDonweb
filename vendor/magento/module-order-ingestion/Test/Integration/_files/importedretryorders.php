<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/**
 * @var \Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface
 */
$orderRepository = $objectManager->get(\Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface::class);

$order = $objectManager->create(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::class);

$order->setOrderId('ORDER_ID');
$order->setSalesChannel('WALLMART');
$order->setExternalOrderId('external-order-1');
$order->setStatus(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::STATUS_RETRY);
$order->setNumberOfRetries(2);
$order->setStoreViewCode('default');
$order->setOrderData(
    '{"orderId":{"id":"ORDER_ID"},"externalId":{"id":"magento-order-2","salesChannel":"WMT"},"createdAt":"2021-10-14T16:46:33Z","updatedAt":"1970-01-01T00:00:00Z","state":"NEW","status":"new","storeViewCode":"default","customerEmail":"demo@examle.com","customerNote":"","shipping":{"shippingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":"Doe"},"shippingMethodName":"DHL","shippingMethodCode":"some-code-123","shippingAmount":12.1,"shippingTax":1.1},"payment":{"billingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":"Doe"},"paymentMethodName":"cc","paymentMethodCode":"some-code-321","totalAmount":123.4,"taxAmount":12,"currency":"USD"},"items":[{"itemId":{"id":"90eff968-b367-4fe7-b3b7-36b06e10683a"},"sku":"NOTFOUND","name":"Some sugar","qty":1,"unitPrice":0,"itemPrice":10,"discountAmount":0,"taxAmount":0.1,"totalAmount":15.6,"weight":1.1,"createdAt":"2021-10-14T16:46:33Z","additionalInformation":[{"name":"some","value":"item data"}]}],"isVirtual":false,"additionalInformation":[{"name":"some","value":"order data"}]}'
);

$orderRepository->save($order);

