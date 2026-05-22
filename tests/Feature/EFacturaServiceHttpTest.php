<?php

namespace Tests\Feature;

use App\Models\IntegrationSetting;
use App\Models\Product;
use App\Services\EFacturaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EFacturaServiceHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_optional_auth_and_origin_headers(): void
    {
        Http::fake([
            'https://demo.example.com/*' => Http::response([
                'success' => true,
                'entity' => 99,
            ], 200),
        ]);

        IntegrationSetting::query()->create([
            'environment_name' => 'Test',
            'base_url' => 'https://demo.example.com',
            'token' => 'secret-token',
            'rut_emisor' => '000000000016',
            'auth_header_value' => 'fixed-auth-value',
            'origin_url' => 'https://app.zafiro.test',
            'default_category_name' => 'Cat',
            'default_prefix' => null,
            'default_margin_percent' => 0,
            'remote_filter_mode' => 'category',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'name' => 'Producto',
            'code' => 'P-HTTP-1',
            'description' => null,
            'category_name' => 'Cat',
            'currency' => 'UYU',
            'cost_minor' => 0,
            'margin_percent' => 10,
            'price_minor' => 1_000,
            'ind_fact_id' => 3,
            'article_type_id' => 1,
            'external_id' => null,
            'sync_status' => 'draft',
            'last_synced_at' => null,
        ]);

        $service = EFacturaService::make();
        $this->assertNotNull($service);

        $result = $service->addArticle($product);

        $this->assertTrue($result['success']);
        $this->assertSame(99, $result['entity']);

        Http::assertSent(function (\Illuminate\Http\Client\Request $request): bool {
            return $request->hasHeader('Authorization', 'Bearer secret-token')
                && $request->hasHeader('RUTEmisor', '000000000016')
                && $request->hasHeader('Auth', 'fixed-auth-value')
                && $request->hasHeader('Origin', 'https://app.zafiro.test');
        });
    }

    public function test_logs_http_status_when_body_is_not_json(): void
    {
        Http::fake([
            'https://demo.example.com/*' => Http::response('Unauthorized', 401, ['Content-Type' => 'text/plain']),
        ]);

        IntegrationSetting::query()->create([
            'environment_name' => 'Test',
            'base_url' => 'https://demo.example.com',
            'token' => 'secret-token',
            'rut_emisor' => '000000000016',
            'auth_header_value' => null,
            'origin_url' => null,
            'default_category_name' => 'Cat',
            'default_prefix' => null,
            'default_margin_percent' => 0,
            'remote_filter_mode' => 'category',
            'is_active' => true,
        ]);

        $product = Product::query()->create([
            'name' => 'Producto',
            'code' => 'P-HTTP-2',
            'description' => null,
            'category_name' => 'Cat',
            'currency' => 'UYU',
            'cost_minor' => 0,
            'margin_percent' => 10,
            'price_minor' => 1_000,
            'ind_fact_id' => 3,
            'article_type_id' => 1,
            'external_id' => null,
            'sync_status' => 'draft',
            'last_synced_at' => null,
        ]);

        $service = EFacturaService::make();
        $this->assertNotNull($service);

        $result = $service->addArticle($product);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('HTTP 401', (string) $result['error']);

        $this->assertDatabaseHas('sync_logs', [
            'product_id' => $product->id,
            'action' => 'addArticle',
            'success' => false,
        ]);
    }
}
