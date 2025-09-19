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

$response = $client->deposit([
    "amount" => 100.00,
    "debtor_name" => "Cliente Teste",
    "email" => "cliente@email.com",
    "debtor_document_number" => "12345678900",
    "phone" => "5511999999999",
    "method_pay" => "pix",
    "postback" => "https://meusite.com/webhook"
]);

print_r($response);
