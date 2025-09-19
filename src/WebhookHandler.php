<?php

namespace SwiftPaySDK;

use Symfony\Component\HttpFoundation\Request;
use SwiftPaySDK\Exceptions\SwiftPayException;

class WebhookHandler
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function handle(Request $request): array
    {
        $payload = $request->getContent();
        $data = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new SwiftPayException("Payload inválido.");
        }

        $signature = $request->headers->get("X-Webhook-Signature");

        if (!$signature || !$this->isValidSignature($payload, $signature)) {
            throw new SwiftPayException("Assinatura inválida no webhook.");
        }

        return $data;
    }

    private function isValidSignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac("sha256", $payload, $this->secret);
        return hash_equals($expected, $signature);
    }
}
