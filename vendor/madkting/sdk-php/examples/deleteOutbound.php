<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Outbound created:<br/>';

require '../vendor/autoload.php';

$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$shop_pk = 18;
$marketplace_pk = 13;
$order_pk = "6843678789533";
$integration_pk = "244dbd3b-c4bb-4013-8caf-ecd05a239446";

$client = new Madkting\MadktingClient($config);

$orderOutbound = $client->serviceOutboundExternal();

$list = $orderOutbound->delete(array(
    'shop_pk' => $shop_pk,
    'marketplace_pk' => $marketplace_pk,
    'order_pk' => $order_pk,
    'integration_pk' => $integration_pk
));
