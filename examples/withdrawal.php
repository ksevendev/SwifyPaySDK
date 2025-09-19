<?php

require __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use SwiftPaySDK\SwiftPayClient;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$client = new SwiftPayClient(
    $_ENV["SWIFTPAY_TOKEN"],
    $_ENV["SWIFTPAY_SECRET"],
    $_ENV["SWIFTPAY_URI"],
    $_ENV["LOG_PATH"]
);

$response = $client->withdraw([
'amount' => 50.00,
'pixKey' => '12345678900',
'pixKeyType' => 'cpf',
'baasPostbackUrl' => 'https://meusite.com/webhook'
]);

print_r($response);
