<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Feed:<br/><br/>';

require '../vendor/autoload.php';

#Token valido
$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$client = new Madkting\MadktingClient($config);
$feedService = $client->serviceFeed();

echo '<pre>';
try {
    $data = $feedService->get(array(
        'feed_pk' => '2EEtH2y0SKeSyuggV1yhXMaWOJVmbaLj1'
    ));
    print_r($data);
} catch (\Madkting\Exception\MadktingException $ex) {
    echo '<b>' . $ex->getMessage() . '</b>';
}
echo '</pre><br/><br/>';