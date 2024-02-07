<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Order list:<br/>';

require '../vendor/autoload.php';

$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$shop_pk = 82;
$marketplace_pk = 13;

$client = new Madkting\MadktingClient($config);

$productService = $client->serviceProduct();

$list = $productService->search(array(
    'shop_pk' => $shop_pk,
));

echo '<table width="100%">';
echo '<tr>';
echo '<td width=100>PK</td><td>DATA</td>';
echo '<tr>';
foreach ($list as $row) {
    echo '<tr>';
    echo '<td>' . $row->pk . '</td><td>' . $row->name . '</td><td style="font-size:11px">' . json_encode($row) . '</td>';
    echo '<tr>';
}
echo '</table>';

echo 'total: ' . count($list);