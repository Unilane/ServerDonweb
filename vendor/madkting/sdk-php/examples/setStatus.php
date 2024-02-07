<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Order list:<br/>';

require '../vendor/autoload.php';

$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$shop_pk = 82;
$marketplace_pk = 13;
$order_pk = '6843678789533';

$client = new Madkting\MadktingClient($config);

$orderService = $client->serviceOrder();


$response = $orderService->setStatus($shop_pk, $marketplace_pk, $order_pk, array(
    'status' => 'shipped',
    'carrier' => "DHL",
    'tracking_code' => "123321"
));




/*
$response = $orderService->setStatus($shop_pk, $marketplace_pk, $order_pk, array(
    'status' => 'canceled',
    'fulfilled' => false,
    'rating' => "neutral",
    'message' => "Pedido Cancelado",
    'reason' => "SELLER_OUT_OF_STOCK",
    'restock_item' => true
));
*/