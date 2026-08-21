<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Client HTTP pour l'API PayDunya (mode "Payment With Redistribution" —
 * page de paiement hébergée, l'utilisateur choisit Wave/Orange Money/
 * Free Money/carte directement sur la page PayDunya).
 *
 * Documentation : https://developers.paydunya.com/doc/FR/http_json
 */
class PayDunyaService
{
    protected string $baseUrl;
    protected string $masterKey;
    protected string $privateKey;
    protected string $token;

    public function __construct()
    {
        $mode = config('services.paydunya.mode', 'test');

        $this->baseUrl = $mode === 'live'
            ? 'https://app.paydunya.com/api/v1'
            : 'https://app.paydunya.com/sandbox-api/v1';

        $this->masterKey = (string) config('services.paydunya.master_key');
        $this->privateKey = (string) config('services.paydunya.private_key');
        $this->token = (string) config('services.paydunya.token');
    }

    protected function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
            'PAYDUNYA-MASTER-KEY' => $this->masterKey,
            'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
            'PAYDUNYA-TOKEN' => $this->token,
        ];
    }

    /**
     * Crée une invoice PayDunya (page de paiement hébergée) et retourne
     * l'URL de paiement (response_text) + le token de l'invoice.
     *
     * @param array{
     *   total_amount: int,
     *   description: string,
     *   store_name: string,
     *   callback_url: string,
     *   return_url: string,
     *   cancel_url: string,
     *   custom_data?: array,
     *   customer_name?: string,
     *   customer_email?: string,
     *   customer_phone?: string,
     * } $data
     * @return array{checkout_url: string, token: string}
     */
    public function createInvoice(array $data): array
    {
        $payload = [
            'invoice' => [
                'total_amount' => (int) $data['total_amount'],
                'description' => $data['description'],
                'custom_data' => $data['custom_data'] ?? [],
            ],
            'store' => [
                'name' => $data['store_name'],
            ],
            'actions' => [
                'callback_url' => $data['callback_url'],
                'return_url' => $data['return_url'],
                'cancel_url' => $data['cancel_url'],
            ],
        ];

        if (!empty($data['customer_name']) || !empty($data['customer_email']) || !empty($data['customer_phone'])) {
            $payload['customer'] = array_filter([
                'name' => $data['customer_name'] ?? null,
                'email' => $data['customer_email'] ?? null,
                'phone' => $data['customer_phone'] ?? null,
            ]);
        }

        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/checkout-invoice/create", $payload);

        $json = $response->json();

        if (!$response->successful() || ($json['response_code'] ?? null) !== '00') {
            Log::error('[PayDunya] Échec création invoice', ['response' => $json]);
            throw new RuntimeException(
                $json['response_text'] ?? $json['description'] ?? 'Erreur lors de la création du paiement PayDunya'
            );
        }

        return [
            'checkout_url' => $json['response_text'],
            'token' => $json['token'],
        ];
    }

    /**
     * Vérifie qu'une notification IPN provient bien de PayDunya : le hash
     * fourni doit être le SHA-512 de la clé maître (master key).
     */
    public function verifyHash(?string $hash): bool
    {
        if (!$hash) {
            return false;
        }

        return hash_equals(hash('sha512', $this->masterKey), $hash);
    }
}
