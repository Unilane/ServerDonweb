<?php

return array(
    'shop' => array(
        'namespace' => 'Shop',
        'endpoints' => array(
            'collection_default' => '{host}/shops/',
            'individual_default' => '{host}/shops/{shop_pk}'
        )
    ),
    'order' => array(
        'namespace' => 'Order',
        'endpoints' => array(
            'collection_default' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/',
            'individual_default' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/',
            'get_shipping_label' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/label/',
            'actions' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/actions/{action}',
            'pending' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/pending/',
            'ready_to_ship' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/ready_to_ship/',
            'shipped' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/ship/',
            'delivered' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/delivered/',
            'packed_by_marketplace' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/packed_by_marketplace/',
            'canceled' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/cancel/',
            'not_delivered' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/no_delivered/',
            'returned' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/returned/'
        )
    ),
    'product' => array(
        'namespace' => 'Product',
        'endpoints' => array(
            'collection_default' => '{host}/shops/{shop_pk}/products/',
            'individual_default' => '{host}/shops/{shop_pk}/products/{product_pk}/'
        )
    ),
    'productimage' => array(
        'namespace' => 'Product',
        'endpoints' => array(
            'collection_default' => '{host}/shops/{shop_pk}/products/{product_pk}/images/',
            'individual_default' => '{host}/shops/{shop_pk}/products/{product_pk}/images/{image_pk}/'
        )
    ),
    'productvariation' => array(
        'namespace' => 'Product',
        'endpoints' => array(
            'collection_default' => '{host}/shops/{shop_pk}/products/{product_pk}/variations/',
            'individual_default' => '{host}/shops/{shop_pk}/products/{product_pk}/variations/{variation_pk}/'
        )
    ),
    'productvariationimage' => array(
        'namespace' => 'Product',
        'endpoints' => array(
            'collection_default' => '{host}/shops/{shop_pk}/products/{product_pk}/variations/{variation_pk}/images/',
            'individual_default' => '{host}/shops/{shop_pk}/products/{product_pk}/variations/{variation_pk}/images/{image_pk}/'
        )
    ),
    'hook' => array(
        'namespace' => 'Hook',
        'endpoints' => array(
            #           'collection_default' => '{host}/hooks/',
#            'individual_default' => '{host}/hooks/{hook_pk}/'
        )
    ),
    'feed' => array(
        'namespace' => 'Feed',
        'endpoints' => array(
            'collection_default' => '{host}/feeds/',
            'individual_default' => '{host}/feeds/{feed_pk}/'
        )
    ),
    'outboundexternal' => array(
        'namespace' => 'Outbound',
        'endpoints' => array(
            'collection_default' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/module-integrations/',
            'individual_default' => '{host}/shops/{shop_pk}/marketplace/{marketplace_pk}/orders/{order_pk}/module-integrations/{integration_pk}/'
        )
    )
);