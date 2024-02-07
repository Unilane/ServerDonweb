<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Outbound created:<br/>';

require '../vendor/autoload.php';

$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$shop_pk = 82;
$marketplace_pk = 13;
$order_pk = "6843678789533";

$client = new Madkting\MadktingClient($config);

$orderOutbound = $client->serviceOutboundExternal();

$list = $orderOutbound->post(array(
    'shop_pk' => $shop_pk,
    'marketplace_pk' => $marketplace_pk,
    'order_pk' => $order_pk,
    'data' => array('module_name' => "Magento",
        'status' => "created",
        "message" => "Order successfull created in magento",
        'reference' => $order_pk,
        "type" => "Sale order",
        //"extra" => []
    )
));

var_dump($list);