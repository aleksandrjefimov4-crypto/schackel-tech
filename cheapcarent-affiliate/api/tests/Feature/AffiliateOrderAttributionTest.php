<?php

namespace Tests\Feature;

use App\Models\AffiliatePartner;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class AffiliateOrderAttributionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_valid_click_is_attributed_once_to_a_core_order(): void
    {
        DB::table('currencies')->insert(['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'value' => 1]);
        $partner = AffiliatePartner::query()->create([
            'user_id' => User::factory()->create()->id,
            'display_name' => 'Travel Blog',
            'website_url' => 'https://example.org',
            'promotion_description' => 'Travel advice for independent drivers.',
            'status' => 'approved',
            'public_code' => 'attributiontest123456',
        ]);

        $visit = $this->get('/api/v1/affiliate/visit/'.$partner->public_code);
        $visit->assertRedirect();
        parse_str((string) parse_url($visit->headers->get('Location'), PHP_URL_QUERY), $query);
        $clickId = $query['affiliate_click_id'] ?? null;
        $this->assertNotNull($clickId);

        $stripe = Mockery::mock(StripeService::class);
        $stripe->shouldReceive('createPaymentSession')->once()->andReturn([
            'status' => 'open', 'url' => 'https://checkout.test/affiliate-session',
        ]);
        $this->app->instance(StripeService::class, $stripe);

        $payload = [
            'isCollection' => false,
            'date' => ['start' => now()->toDateString(), 'end' => now()->toDateString()],
            'currency_code' => 'EUR',
            'driver' => ['first_name' => 'Test', 'last_name' => 'Driver'],
            'customer' => ['email' => 'affiliate-test@example.org', 'phone' => '+37255500000'],
            'product' => ['location' => 'Test city', 'brand' => 'Test', 'model' => 'Car'],
            'total' => 100,
            'affiliate_click_id' => $clickId,
        ];
        $requestKey = 'affiliate-test-'.Str::uuid();

        $this->withHeader('Idempotency-Key', $requestKey)
            ->postJson('/api/v1/order/create', $payload)->assertCreated();
        $this->withHeader('Idempotency-Key', $requestKey)
            ->postJson('/api/v1/order/create', $payload)->assertCreated();

        $this->assertSame(1, DB::table('affiliate_order_attributions')
            ->where('affiliate_partner_id', $partner->id)
            ->where('affiliate_click_id', $clickId)->count());
    }
}
