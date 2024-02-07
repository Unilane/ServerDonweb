<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Test Token:<br/><br/>';

require '../vendor/autoload.php';

echo '<b>Valid Token</b>:<br/>';
// Token valido
$config = array('token' => '22401a14e1c058f16159c50821e15dcaa2ac4c89');
$client = new Madkting\MadktingClient($config);
$response = $client->testToken();

echo '<pre>';
print_r($response);
echo '</pre><br/><br/>';


echo '<b>Valid Token</b>:<br/>';
// Token invalido
$config = array('token' => 'asdasdasdas');
$client = new Madkting\MadktingClient($config);
try {
    $response = $client->testToken();
} catch (Exception $ex) {
    echo '<pre>';
    echo 'Token no valido';
    echo '</pre>';
}