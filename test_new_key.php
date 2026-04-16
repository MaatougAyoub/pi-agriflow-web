<?php
require 'vendor/autoload.php';
use Symfony\Component\HttpClient\HttpClient;

$key = 'AIzaSyB-nLzN4CNrsS5NF1eKTtFxkxr3dtbXO2Q';
$model = 'gemini-1.5-flash';
$url = sprintf('https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s', $model, $key);

$client = HttpClient::create();
$response = $client->request('POST', $url, [
    'json' => ['contents' => [['parts' => [['text' => 'hello']]]]],
    'verify_peer' => false,
]);

echo $response->getStatusCode() . "\n";
print_r($response->toArray(false));
