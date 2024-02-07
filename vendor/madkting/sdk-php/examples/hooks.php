<?php

/**
 * Request Example without body
 * POST http://localhost/madkting-sdk-php/examples/hooks.php
        User-Agent: mad-hookshot/random$
        X-Madkting-event: Order:created
        X-Madkting-signature: r24r3f34fr45v32cvefc2
        X-Madkting-delivery: aG9sYW11bmRv
        Location: http://api.software.madkting.com/shops/2/marketplaces/13/orders/MLM1221/
        Content-Type: application/json
 *
 *
 * Request Example with body
 * POST http://localhost/madkting-sdk-php/examples/hooks.php
        user-agent: mad-hookshot/random$
        X-Madkting-event: Order:created
        X-Madkting-signature: r24r3f34fr45v32cvefc2
        X-Madkting-delivery: aG9sYW11bmRv
        location: http://api.software.madkting.com/shops/2/marketplaces/13/orders/MLM1221/
        Content-Type: application/json
        {"pk": "MLM12332"}
 */


error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Hook detection:<br/><br/>';

require '../vendor/autoload.php';

#Token valido
$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$client = new Madkting\MadktingClient($config);
$hookService = $client->serviceHook();

echo '<pre>';
try {
    $data = $hookService->detect();
    print_r($data['location']);

    if (!empty($data['location'])) {
        try {
            $response = $client->exec($data['location']);
            print_r($response);
        } catch (Exception $ex) {
            print('ERROR:' . $ex->getMessage());
        }
    }
} catch (Exception $ex) {
    echo '<b>' . $ex->getMessage() . '</b>';
}
echo '</pre><br/><br/>';