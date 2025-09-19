<?php

namespace SwiftPaySDK;

use GuzzleHttp\Client;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;
use SwiftPaySDK\Exceptions\SwiftPayException;

class SwiftPayClient
{
    private Client $http;
    private Logger $logger;
    private string $token;
    private string $secret;
    private string $baseUrl;

    public function __construct(string $token, string $secret, string $baseUrl, string $logPath)
    {
        $this->token = $token;
        $this->secret = $secret;
        $this->baseUrl = $baseUrl;

        $this->http = new Client([
            "base_uri" => $this->baseUrl,
            "timeout" => 10,
            "headers" => [
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ]
        ]);

        $this->logger = new Logger("swiftpay");
        $this->logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
    }

    public function deposit(array $data): array
    {
        $this->validateDeposit($data);
        return $this->request("wallet/deposit/payment", $data);
    }

    public function withdraw(array $data): array
    {
        $this->validateWithdraw($data);
        return $this->request("pixout", $data);
    }

    private function request(string $endpoint, array $payload): array
    {
        $payload["token"] = $this->token;
        $payload["secret"] = $this->secret;
        $traceId = Uuid::uuid4()->toString();

        try {
            $response = $this->http->post($endpoint, ["json" => $payload]);
            $body = json_decode((string)$response->getBody(), true);

            $this->logger->info("[$traceId] Sucesso", ["endpoint" => $endpoint, "payload" => $payload, "response" => $body]);

            return $body;
        } catch (\Exception $e) {
            $this->logger->error("[$traceId] Erro", ["endpoint" => $endpoint, "payload" => $payload, "error" => $e->getMessage()]);
            throw new SwiftPayException("Erro na requisição: " . $e->getMessage());
        }
    }

    private function validateDeposit(array $data): void
    {
        if (!v::stringType()->notEmpty()->validate($data["debtor_name"] ?? null)) {
            throw new SwiftPayException("Nome do devedor inválido.");
        }
        if (!v::email()->validate($data["email"] ?? null)) {
            throw new SwiftPayException("Email inválido.");
        }
        if (!v::numeric()->positive()->validate($data["amount"] ?? null)) {
            throw new SwiftPayException("Valor do depósito inválido.");
        }
    }

    private function validateWithdraw(array $data): void
    {
        if (!v::numeric()->positive()->validate($data["amount"] ?? null)) {
            throw new SwiftPayException("Valor do saque inválido.");
        }
        if (!v::stringType()->notEmpty()->validate($data["pixKey"] ?? null)) {
            throw new SwiftPayException("Chave PIX inválida.");
        }
    }
}
