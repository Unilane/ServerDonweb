<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Order list:<br/>';

require '../vendor/autoload.php';

$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$shop_pk = 82;
$marketplace_pk = 13;
$order_pk;

$client = new Madkting\MadktingClient($config);

$orderService = $client->serviceOrder();

$list = $orderService->search(array(
    'shop_pk' => $shop_pk,
    'marketplace_pk' => $marketplace_pk
));

echo '<table width="100%">';
echo '<tr>';
echo '<td width=100>PK</td><td width=100>CUSTOMER</td><td width=100>STATUS</td><td width=100>IS PAID</td><td>DATA</td>';
echo '<tr>';
foreach ($list as $row) {
    echo '<tr>';
    echo '<td>' . $row->pk . '</td><td>' . $row->customer->email . '</td><td>' . $orderService->getStatus($row) . '</td><td>' . $orderService->isPaid($row) . '</td><td style="font-size:11px">' . json_encode($row) . '</td>';
    echo '<tr>';
    $order_pk = $row->pk;
}
echo '</table>';

echo 'total: ' . count($list);

if ($order_pk) {
    echo 'Get one:<br/>';

    $order = $orderService->get(array(
        'shop_pk' => $shop_pk,
        'marketplace_pk' => $marketplace_pk,
        'order_pk' => $order_pk
    ));
    echo '<b>status:</b>' . $orderService->getStatus($order) . '<br/>';
    echo json_encode($order);
}