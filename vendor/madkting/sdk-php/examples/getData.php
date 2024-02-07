<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Test Token:<br/><br/>';

require '../vendor/autoload.php';

$status = Madkting\MadktingClient::getOrderStatusDictionary();

echo '<b>Order status:</b><br/>';
echo '<pre>';
echo print_r($status, true);
echo '</pre><br/><br/>';


$fields = Madkting\MadktingClient::getProductFieldsDictionary();

echo '<b>Product fields:</b><br/>';
echo '<pre>';
echo print_r($fields, true);
echo '</pre><br/><br/>';


$marketplaces = Madkting\MadktingClient::getMarketplacesList();

echo '<b>Marketplaces:</b><br/>';
echo '<pre>';
echo print_r($marketplaces, true);
echo '</pre><br/><br/>';


$categories = Madkting\MadktingClient::getCategoriesList();

echo '<b>Categories:</b><br/>';
echo '<pre>';
echo print_r($categories, true);
echo '</pre><br/><br/>';