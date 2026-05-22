<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use App\Models\Product;
use App\Models\SyncLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EFacturaService
{
    public function __construct(
        private IntegrationSetting $settings,
    ) {}

    public static function make(?int $tenantId = null): ?self
    {
        $settings = IntegrationSetting::active($tenantId);

        if (! $settings) {
            return null;
        }

        return new self($settings);
    }

    /**
     * @return array{success: bool, entity: ?int, error: ?string}
     */
    public function addArticle(Product $product): array
    {
        $payload = $this->buildCreatePayload($product);

        return $this->send('POST', '/api/extsys/addArticle', $payload, $product, 'addArticle');
    }

    /**
     * @return array{success: bool, entity: ?int, error: ?string}
     */
    public function updateArticle(Product $product): array
    {
        if (! $product->hasExternalId()) {
            return [
                'success' => false,
                'entity' => null,
                'error' => 'El producto no tiene external_id asignado.',
            ];
        }

        $payload = $this->buildUpdatePayload($product);

        return $this->send('PUT', '/api/extsys/updateArticle', $payload, $product, 'updateArticle');
    }

    /**
     * @return array{success: bool, entity: ?int, error: ?string}
     */
    private function send(string $method, string $endpoint, array $payload, Product $product, string $action): array
    {
        $url = rtrim($this->settings->base_url, '/').$endpoint;

        try {
            $response = Http::withHeaders($this->integrationRequestHeaders())
                ->timeout(30)
                ->$method($url, $payload);

            $status = $response->status();
            $parsed = $response->json();
            $rawBody = $response->body();

            $responsePayload = $this->buildResponsePayloadForLog($status, $parsed, $rawBody);

            $success = $response->successful()
                && is_array($parsed)
                && ($parsed['success'] ?? false);

            $entity = is_array($parsed) ? ($parsed['entity'] ?? null) : null;

            $errorMessage = $this->resolveApiErrorMessage($success, $status, $parsed, $rawBody);

            $this->log($product, $action, $payload, $responsePayload, $success, $errorMessage);

            if ($success && $action === 'addArticle' && $entity) {
                $product->update([
                    'external_id' => $entity,
                    'sync_status' => 'synced',
                    'last_synced_at' => now(),
                ]);
            } elseif ($success && $action === 'updateArticle') {
                $product->update([
                    'sync_status' => 'synced',
                    'last_synced_at' => now(),
                ]);
            } elseif (! $success) {
                $product->update(['sync_status' => 'error']);
            }

            return [
                'success' => $success,
                'entity' => $entity,
                'error' => $errorMessage,
            ];
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();

            $this->log($product, $action, $payload, ['exception' => $errorMessage], false, $errorMessage);

            $product->update(['sync_status' => 'error']);

            return [
                'success' => false,
                'entity' => null,
                'error' => $errorMessage,
            ];
        }
    }

    /**
     * @return array<string, string>
     */
    private function integrationRequestHeaders(): array
    {
        $headers = [
            'Authorization' => 'Bearer '.$this->settings->token,
            'RUTEmisor' => $this->settings->rut_emisor,
        ];

        if (filled($this->settings->auth_header_value)) {
            $headers['Auth'] = $this->settings->auth_header_value;
        }

        if (filled($this->settings->origin_url)) {
            $headers['Origin'] = $this->settings->origin_url;
        }

        return $headers;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildResponsePayloadForLog(int $status, mixed $parsed, string $rawBody): array
    {
        if (is_array($parsed)) {
            return array_merge($parsed, ['http_status' => $status]);
        }

        return [
            'http_status' => $status,
            'raw_body' => Str::limit($rawBody, 2000),
            'json_parsed' => false,
        ];
    }

    private function resolveApiErrorMessage(bool $success, int $status, mixed $parsed, string $rawBody): ?string
    {
        if ($success) {
            return null;
        }

        if (! is_array($parsed)) {
            $detail = trim($rawBody) === '' ? 'cuerpo vacío o no JSON' : 'respuesta no JSON';

            return sprintf('HTTP %d: %s', $status, $detail);
        }

        $detail = (string) ($parsed['errorMessage'] ?? $parsed['message'] ?? 'Respuesta inesperada');

        return sprintf('HTTP %d: %s', $status, $detail);
    }

    private function buildCreatePayload(Product $product): array
    {
        $prefix = $this->settings->default_prefix ?? '';
        $code = str_starts_with($product->code, $prefix) ? $product->code : $prefix.$product->code;

        return [
            'name' => $product->name,
            'desc' => $product->description ?? '',
            'price' => round($product->price_minor / 100, 2),
            'monId' => $product->mon_id,
            'groupName' => $this->settings->default_category_name ?? $product->category_name,
            'indFactId' => $product->ind_fact_id,
            'articleTypeId' => $product->article_type_id,
            'articleCodes' => [
                [
                    'CodeType' => 1,
                    'Code' => $code,
                ],
            ],
        ];
    }

    /**
     * Solo los campos que updateArticle realmente actualiza.
     */
    private function buildUpdatePayload(Product $product): array
    {
        $prefix = $this->settings->default_prefix ?? '';
        $code = str_starts_with($product->code, $prefix) ? $product->code : $prefix.$product->code;

        return [
            'id' => $product->external_id,
            'name' => $product->name,
            'desc' => $product->description ?? '',
            'price' => round($product->price_minor / 100, 2),
            'monId' => $product->mon_id,
            'groupName' => '',
            'indFactId' => $product->ind_fact_id,
            'articleTypeId' => $product->article_type_id,
            'articleCodes' => [
                [
                    'CodeType' => 1,
                    'Code' => $code,
                ],
            ],
        ];
    }

    private function log(
        Product $product,
        string $action,
        array $requestPayload,
        ?array $responsePayload,
        bool $success,
        ?string $errorMessage,
    ): void {
        SyncLog::create([
            'tenant_id' => $product->tenant_id,
            'product_id' => $product->id,
            'action' => $action,
            'request_payload' => $requestPayload,
            'response_payload' => $responsePayload,
            'success' => $success,
            'error_message' => $errorMessage,
            'created_at' => now(),
        ]);
    }
}
