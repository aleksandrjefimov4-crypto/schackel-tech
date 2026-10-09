<?php

namespace Tests\Feature;

use App\Models\AffiliatePartner;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AffiliateApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_read_partner_data(): void
    {
        foreach (['/api/v1/affiliate/cabinet', '/api/v1/affiliate/tools', '/api/v1/affiliate/report', '/api/v1/affiliate/catalog'] as $path) {
            $this->getJson($path)->assertUnauthorized();
        }
    }

    public function test_only_approved_partner_can_generate_and_use_a_safe_link(): void
    {
        $partner = AffiliatePartner::query()->create([
            'user_id' => User::factory()->create()->id,
            'display_name' => 'Travel Blog',
            'website_url' => 'https://example.org',
            'promotion_description' => 'Travel advice for independent drivers.',
            'status' => 'approved',
            'public_code' => 'testaffiliate12345678',
        ]);

        $this->actingAs($partner->user ?? User::findOrFail($partner->user_id), 'api')
            ->getJson('/api/v1/affiliate/tools?to=%2Fen%2Fcars&sub_id=article_1')
            ->assertOk()->assertJsonStructure(['url']);

        $this->get('/api/v1/affiliate/visit/'.$partner->public_code.'?to=%2Fen%2Fcars')
            ->assertRedirect();
        $firstClickId = DB::table('affiliate_clicks')->where('affiliate_partner_id', $partner->id)->value('id');
        $this->get('/api/v1/affiliate/visit/'.$partner->public_code.'?to=%2Fen%2Fcars')
            ->assertRedirect('https://cheapcarent.com/en/cars?affiliate_click_id='.$firstClickId);
        $this->assertSame(1, DB::table('affiliate_clicks')->where('affiliate_partner_id', $partner->id)->count());

        $this->get('/api/v1/affiliate/visit/'.$partner->public_code.'?to=https%3A%2F%2Fevil.example')
            ->assertStatus(422);
        $this->assertSame(1, DB::table('affiliate_clicks')->where('affiliate_partner_id', $partner->id)->count());

        $partner->update(['status' => 'rejected']);
        $this->get('/api/v1/affiliate/visit/'.$partner->public_code)->assertNotFound();
        $this->actingAs(User::findOrFail($partner->user_id), 'api')
            ->getJson('/api/v1/affiliate/tools')->assertNotFound();
        $this->actingAs(User::findOrFail($partner->user_id), 'api')
            ->getJson('/api/v1/affiliate/report')->assertNotFound();
    }

    public function test_another_partner_cannot_read_the_first_partners_report_or_link(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();
        $first = AffiliatePartner::query()->create([
            'user_id' => $firstUser->id,
            'display_name' => 'First Blog',
            'website_url' => 'https://first.example',
            'promotion_description' => 'Travel advice for independent drivers.',
            'status' => 'approved',
            'public_code' => 'firstaffiliate1234567',
        ]);
        AffiliatePartner::query()->create([
            'user_id' => $secondUser->id,
            'display_name' => 'Second Blog',
            'website_url' => 'https://second.example',
            'promotion_description' => 'Travel advice for independent drivers.',
            'status' => 'approved',
            'public_code' => 'secondaffiliate123456',
        ]);

        $this->get('/api/v1/affiliate/visit/'.$first->public_code)->assertRedirect();
        $this->actingAs($secondUser, 'api')->getJson('/api/v1/affiliate/report')
            ->assertOk()->assertJsonPath('clicks', 0);
        $this->actingAs($secondUser, 'api')->getJson('/api/v1/affiliate/tools')
            ->assertOk()->assertDontSee($first->public_code);
    }

    public function test_report_contains_only_aggregate_counts(): void
    {
        $user = User::factory()->create();
        AffiliatePartner::query()->create([
            'user_id' => $user->id,
            'display_name' => 'Travel Blog',
            'website_url' => 'https://example.org',
            'promotion_description' => 'Travel advice for independent drivers.',
            'status' => 'approved',
            'public_code' => 'testaffiliate87654321',
        ]);

        $response = $this->actingAs($user, 'api')->getJson('/api/v1/affiliate/report');
        $response->assertOk()->assertExactJson([
            'period_days' => 30,
            'clicks' => 0,
            'orders' => 0,
            'paid_orders' => 0,
            'completed_orders' => 0,
            'earnings_available' => false,
            'payouts_available' => false,
        ]);
    }

    public function test_catalog_requires_approved_affiliate(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api')->getJson('/api/v1/affiliate/catalog')->assertNotFound();

        $partner = AffiliatePartner::query()->create([
            'user_id' => $user->id,
            'display_name' => 'Travel Blog',
            'website_url' => 'https://example.org',
            'promotion_description' => 'Travel advice for independent drivers.',
            'status' => 'approved',
            'public_code' => 'testaffiliatecatalog01',
        ]);
        $this->actingAs($user, 'api')->getJson('/api/v1/affiliate/catalog')
            ->assertOk()->assertJsonStructure(['data', 'pagination' => ['page', 'last_page', 'total']]);

        $partner->update(['status' => 'rejected']);
        $this->actingAs($user, 'api')->getJson('/api/v1/affiliate/catalog')->assertNotFound();
    }
}
