<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Order list:<br/>';

require '../vendor/autoload.php';

$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$shop_pk = 82;
$marketplace_pk = 13;

$product = array(
    'sku_simple' => '3940445',
    'sku' => '3940445-032',
    'color' => 'azul',
    'price' => 350,
    #    'pk' => 31968,
    'size' => 's',
    'stock' => 10,
    'availability' => 'in_stock',
    'discount' => NULL,
    'discount_from' => NULL,
    'discount_to' => NULL,
    'images' => array(),
    'upc' => '750104790301',
    'name' => 'Verduras congeladas La Huerta mezcla California 500 g',
    'brand' => 'Chedraui',
    'condition' => 'new',
    'description' => 'Verduras congeladas La Huerta mezcla california 500 g. Brócoli, coliflor y zanahoria. Sin conservadores ni colorantes. Certificación Kosher.',
    'dimensions_unit' => 'cm',
    'category_pk' => 524,
    'shipping_depth' => 12,
    'shipping_height' => 11,
    'shipping_width' => 12,
    'shipping' => 0,
    'weight' => 21,
    'weight_unit' => 'kg');


$client = new Madkting\MadktingClient($config);

$productService = $client->serviceProduct();

try {
    $feed_location = $productService->post(array(
        'shop_pk' => $shop_pk,
        'products' => array($product)
    ));

    echo 'FEED LOCATION ' . $feed_location;
} catch (Madkting\Exception\MadktingException $ex) {
    echo $ex->getMessage();
    echo '<br/>';
    echo $ex->getResponse();
} catch (Exception $ex) {
    echo $ex->getMessage();
}


// ADD Variation


$product_pk = 26355420;
$variation1 = array(
    'sku' => '112233-01',
    'color' => 'negro',
    'price' => 330,
    'size' => 's',
    'stock' => 1,
    'availability' => 'in_stock'
);
$variation2 = array(
    'sku' => '112233-02',
    'color' => 'verde',
    'price' => 365,
    'size' => 'm',
    'stock' => 15,
    'availability' => 'in_stock',
    'discount' => 10
);

$variationService = $client->serviceProductVariation();

try {
    $feed_location = $variationService->post(array(
        'shop_pk' => $shop_pk,
        'product_pk' => $product_pk,
        'variations' => array($variation1, $variation2)
    ));

    echo 'FEED LOCATION ' . $feed_location;
} catch (Madkting\Exception\MadktingException $ex) {
    echo $ex->getMessage();
    echo '<pre>';
    print_r($ex->getRequest());

} catch (Exception $ex) {
    echo $ex->getMessage();
}

/* PRODUCT IMAGES **/

$imagesService = $client->serviceProductImage();
try {
    $feed_location = $imagesService->post(array(
        'shop_pk' => $shop_pk,
        'product_pk' => $product_pk,
        'images' => array(
            array('url' => 'http://grupojaquete.com/wp-content/uploads/2013/02/verdura-grupo-jaquete-940x473.jpg'),
            array('url' => 'https://i.ytimg.com/vi/GRpGworcM9A/maxresdefault.jpg')
        )
    ));

    echo 'FEED LOCATION ' . $feed_location;
} catch (Madkting\Exception\MadktingException $ex) {
    echo $ex->getMessage();
    echo '<pre>';
    print_r($ex->getResponse());

} catch (Exception $ex) {
    echo $ex->getMessage();
}