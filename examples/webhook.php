<?php

require __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use SwiftPaySDK\WebhookHandler;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$request = Request::createFromGlobals();

try {
    $handler = new WebhookHandler($_ENV["WEBHOOK_SECRET"]);
    $data = $handler->handle($request);

    file_put_contents($_ENV["LOG_PATH"], "[".date("Y-m-d H:i:s")."] ".json_encode($data).PHP_EOL, FILE_APPEND);

    http_response_code(200);
    echo json_encode(["success" => true]);
} catch (\Exception $e) {
    http_response_code(400);
    echo json_encode(["error" => $e->getMessage()]);
}
