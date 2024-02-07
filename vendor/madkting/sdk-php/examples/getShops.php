<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Shop list:<br/>';

require '../vendor/autoload.php';

$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$client = new Madkting\MadktingClient($config);

$shopService = $client->serviceShop();

$list = $shopService->search();

echo '<table width="100%">';
echo '<tr>';
echo '<td width=100>PK</td><td width=100>NAME</td><td>DATA</td>';
echo '<tr>';
foreach ($list as $shop) {
    echo '<tr>';
    echo '<td>' . $shop->pk . '</td><td>' . $shop->name . '</td><td style="font-size:11px">' . json_encode($shop) . '</td>';
    echo '<tr>';
}
echo '</table>';

echo 'total: ' . count($list);