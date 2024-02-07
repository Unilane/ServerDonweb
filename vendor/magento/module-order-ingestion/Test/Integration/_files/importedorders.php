<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/**
 * @var \Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface
 */
$orderRepository = $objectManager->get(\Magento\OrderIngestion\Api\ExternalOrderRepositoryInterface::class);

$order = $objectManager->create(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::class);

$order->setOrderId('ORDER_ID_WITH_TAX');
$order->setSalesChannel('WALLMART');
$order->setExternalOrderId('external-order-1');
$order->setStatus(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::STATUS_RECEIVED);
$order->setStoreViewCode('default');
$order->setOrderData(
    '{"orderId":{"id":"ORDER_ID_WITH_TAX"},"externalId":{"id":"magento-order-1","salesChannel":"WMT"},"createdAt":"2021-10-14T16:46:33Z","updatedAt":"1970-01-01T00:00:00Z","state":"NEW","status":"new","storeViewCode":"default","customerEmail":"demo@examle.com","customerNote":"","shipping":{"shippingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":"Doe"},"shippingMethodName":"DHL","shippingMethodCode":"some-code-123","shippingAmount":12.1,"shippingTax":1.1},"payment":{"billingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":"Doe"},"paymentMethodName":"cc","paymentMethodCode":"some-code-321","totalAmount":123.4,"taxAmount":12,"currency":"USD"},"items":[{"itemId":{"id":"90eff968-b367-4fe7-b3b7-36b06e10683a"},"sku":"simple","name":"Some sugar","qty":1,"unitPrice":0,"itemPrice":10,"discountAmount":0,"taxAmount":0.1,"totalAmount":10.1,"weight":1.1,"createdAt":"2021-10-14T16:46:33Z","additionalInformation":[{"name":"some","value":"item data"}]},{"itemId":{"id":"90eff968-b367-4fe7-b3b7-36b06e10683b"},"sku":"simple","name":"Some sugar","qty":1,"unitPrice":0,"itemPrice":10,"discountAmount":0,"taxAmount":0.1,"totalAmount":10.1,"weight":1.1,"createdAt":"2021-10-14T16:46:33Z","additionalInformation":[{"name":"some","value":"item data"}]}],"isVirtual":false,"additionalInformation":[{"name":"some","value":"order data"}]}'
);

$orderRepository->save($order);


$order2 = $objectManager->create(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::class);

$order2->setOrderId('ORDER_ID_BAD_STORE');
$order2->setSalesChannel('WALLMART');
$order2->setExternalOrderId('external-order-2');
$order2->setStatus(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::STATUS_IMPORTED);
$order2->setStoreViewCode('default');
$order2->setOrderData(
    '{"orderId":{"id":"ORDER_ID_BAD_STORE"},"externalId":{"id":"magento-order-2","salesChannel":"WMT"},"createdAt":"2021-10-14T16:46:33Z","updatedAt":"1970-01-01T00:00:00Z","state":"NEW","status":"new","storeViewCode":"default","customerEmail":"demo@examle.com","customerNote":"","shipping":{"shippingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":"Doe"},"shippingMethodName":"DHL","shippingMethodCode":"some-code-123","shippingAmount":12.1,"shippingTax":1.1},"payment":{"billingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":"Doe"},"paymentMethodName":"cc","paymentMethodCode":"some-code-321","totalAmount":123.4,"taxAmount":12,"currency":"USD"},"items":[{"itemId":{"id":"90eff968-b367-4fe7-b3b7-36b06e10683a"},"sku":"simple","name":"Some sugar","qty":1,"unitPrice":0,"itemPrice":10,"discountAmount":0,"taxAmount":0.1,"totalAmount":10.1,"weight":1.1,"createdAt":"2021-10-14T16:46:33Z","additionalInformation":[{"name":"some","value":"item data"}]}],"isVirtual":false,"additionalInformation":[{"name":"some","value":"order data"}]}'
);

$orderRepository->save($order2);


$order3 = $objectManager->create(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::class);

$order3->setOrderId('ORDER_ID_BAD_LASTNAME');
$order3->setSalesChannel('WALLMART');
$order3->setExternalOrderId('external-order-1-bad-last-name');
$order3->setStatus(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::STATUS_RECEIVED);
$order3->setStoreViewCode('default');
$order3->setOrderData(
    '{"orderId":{"id":"ORDER_ID_BAD_LASTNAME"},"externalId":{"id":"magento-order-1","salesChannel":"WMT"},"createdAt":"2021-10-14T16:46:33Z","updatedAt":"1970-01-01T00:00:00Z","state":"NEW","status":"new","storeViewCode":"default","customerEmail":"demo@examle.com","customerNote":"","shipping":{"shippingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":""},"shippingMethodName":"DHL","shippingMethodCode":"some-code-123","shippingAmount":12.1,"shippingTax":1.1},"payment":{"billingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":""},"paymentMethodName":"cc","paymentMethodCode":"some-code-321","totalAmount":123.4,"taxAmount":12,"currency":"USD"},"items":[{"itemId":{"id":"90eff968-b367-4fe7-b3b7-36b06e10683a"},"sku":"simple","name":"Some sugar","qty":1,"unitPrice":0,"itemPrice":10,"discountAmount":0,"taxAmount":0.1,"totalAmount":10.1,"weight":1.1,"createdAt":"2021-10-14T16:46:33Z","additionalInformation":[{"name":"some","value":"item data"}]}],"isVirtual":false,"additionalInformation":[{"name":"some","value":"order data"}]}'
);

$orderRepository->save($order3);

$order4 = $objectManager->create(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::class);

$order4->setOrderId('ORDER_ID_WITHOUT_TAX');
$order4->setSalesChannel('WALLMART');
$order4->setExternalOrderId('order_without_item_tax');
$order4->setStatus(\Magento\OrderIngestion\Api\Data\ExternalOrderInterface::STATUS_RECEIVED);
$order4->setStoreViewCode('default');
$order4->setOrderData(
    '{"orderId":{"id":"ORDER_ID_WITHOUT_TAX"},"externalId":{"id":"order_without_item_tax","salesChannel":"WMT"},"createdAt":"2021-10-14T16:46:33Z","updatedAt":"1970-01-01T00:00:00Z","state":"NEW","status":"new","storeViewCode":"default","customerEmail":"demo@examle.com","customerNote":"","shipping":{"shippingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":"Doe"},"shippingMethodName":"DHL","shippingMethodCode":"some-code-123","shippingAmount":12.1,"shippingTax":0},"payment":{"billingAddress":{"phone":"512-000-00-88","region":"TX","postcode":"78758","street":"11501 Domain Drive","city":"Austin","country":"US","firstname":"John","lastname":"Doe"},"paymentMethodName":"cc","paymentMethodCode":"some-code-321","totalAmount":123.4,"taxAmount":12,"currency":"USD"},"items":[{"itemId":{"id":"90eff968-b367-4fe7-b3b7-36b06e10683a"},"sku":"simple","name":"Some sugar","qty":1,"unitPrice":0,"itemPrice":10,"discountAmount":0,"taxAmount":0,"totalAmount":10,"weight":1.1,"createdAt":"2021-10-14T16:46:33Z","additionalInformation":[{"name":"some","value":"item data"}]}],"isVirtual":false,"additionalInformation":[{"name":"some","value":"order data"}]}'
);

$orderRepository->save($order4);
