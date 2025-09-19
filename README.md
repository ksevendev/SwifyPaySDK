# SwiftPay SDK & API PIX

SDK PHP para integração com a API PIX da SwiftPay, incluindo métodos para depósitos, saques e processamento seguro de webhooks.

---

## Instalação

```bash
composer require kseven/swiftpay-sdk
```

Certifique-se de ter PHP >= 8.0 e Guzzle 7.

## Configuração

Crie um arquivo `.env` na raiz do projeto:

```
SWIFTPAY_URI=https://swiftpay.com.br/api/
SWIFTPAY_TOKEN=seu_token
SWIFTPAY_SECRET=seu_secret
WEBHOOK_SECRET=seu_webhook_secret
LOG_PATH=./logs/swiftpay.log
```

Crie a pasta `logs` com permissão de escrita.

## Estrutura de arquivos

```
swiftpay-sdk/
├─ public/
│  ├─ webhook.php
│  ├─ deposit.php
│  └─ withdraw.php
├─ src/
│  ├─ SwiftPayClient.php
│  ├─ WebhookHandler.php
│  └─ Exceptions/SwiftPayException.php
├─ logs/
├─ .env
└─ composer.json
```

## Uso do SDK

### Depósito (PIX IN)

```php
$client = new SwiftPayClient(
    $_ENV['SWIFTPAY_TOKEN'],
    $_ENV['SWIFTPAY_SECRET'],
    $_ENV['SWIFTPAY_URI'],
    $_ENV['LOG_PATH']
);

$response = $client->deposit([
    'amount' => 100.00,
    'debtor_name' => 'Cliente Teste',
    'email' => 'cliente@email.com',
    'debtor_document_number' => '12345678900',
    'phone' => '5511999999999',
    'method_pay' => 'pix',
    'postback' => 'https://meusite.com/webhook'
]);

print_r($response);
```

### Saque (PIX OUT)

```php
$response = $client->withdraw([
    'amount' => 50.00,
    'pixKey' => '12345678900',
    'pixKeyType' => 'cpf',
    'baasPostbackUrl' => 'https://meusite.com/webhook'
]);

print_r($response);
```

### Webhook

Exemplo de endpoint em PHP usando `WebhookHandler`:

```php
$request = Request::createFromGlobals();
$handler = new WebhookHandler($_ENV['WEBHOOK_SECRET']);
$data = $handler->handle($request);
file_put_contents($_ENV['LOG_PATH'], json_encode($data), FILE_APPEND);
http_response_code(200);
echo json_encode(['success' => true]);
```

## API Direta — Referência

### Depósito (PIX IN)

- **POST** `https://swiftpay.com.br/api/wallet/deposit/payment`
- **Headers:** `Content-Type: application/json`, `Accept: application/json`
- **Body:**

```json
{
  "token": "seu_token",
  "secret": "seu_secret",
  "postback": "rota_callback",
  "amount": 100.00,
  "debtor_name": "Nome",
  "email": "email@dominio.com",
  "debtor_document_number": "CPF",
  "phone": "Telefone",
  "method_pay": "pix"
}
```
- **Response:**

```json
{
  "idTransaction": "TX123",
  "qrcode": "código",
  "qr_code_image_url": "url"
}
```

### Saque (PIX OUT)

- **POST** `https://swiftpay.com.br/api/pixout`
- **Headers:** `Content-Type: application/json`, `Accept: application/json`
- **Body:**

```json
{
  "token": "seu_token",
  "secret": "seu_secret",
  "baasPostbackUrl": "url_callback",
  "amount": 100.00,
  "pixKey": "chave_pix",
  "pixKeyType": "cpf"
}
```
- **Response:**

```json
{
  "id": "uuid",
  "amount": 100,
  "pixKey": "chave",
  "pixKeyType": "cpf",
  "withdrawStatusId": "PendingProcessing"
}
```

### Webhook

```json
{
  "nome": "Cliente Teste",
  "cpf": "12345678900",
  "email": "cliente@email.com",
  "status": "pago"
}
```

## Segurança

- Nunca coloque tokens diretamente no código; use `.env`
- Valide HMAC dos webhooks
- Use HTTPS em todas as requisições
- Restrinja IPs quando possível
- Mantenha logs ativos para auditoria

---

*Gerado em 19/09/2025 14:05 (America/Sao_Paulo)*
