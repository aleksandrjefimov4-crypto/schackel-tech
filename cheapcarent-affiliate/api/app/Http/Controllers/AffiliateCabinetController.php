<?php

namespace App\Http\Controllers;

use App\Models\AffiliatePartner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliateCabinetController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $partner = AffiliatePartner::query()->where('user_id', $request->user()->id)->first();

        return response()->json([
            'partner' => $partner ? $this->publicPartner($partner) : null,
            'tracking_available' => $partner?->status === 'approved',
            'links_available' => $partner?->status === 'approved',
            'payouts_available' => false,
        ]);
    }

    public function apply(Request $request): JsonResponse
    {
        $data = $request->validate([
            'display_name' => ['required', 'string', 'min:2', 'max:160'],
            'website_url' => ['required', 'url', 'max:500', 'regex:/^https?:\/\//i'],
            'promotion_description' => ['required', 'string', 'min:20', 'max:1000'],
        ]);

        if (AffiliatePartner::query()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'An affiliate application already exists.'], 409);
        }

        $partner = AffiliatePartner::query()->create([
            ...$data,
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'public_code' => Str::lower(Str::random(20)),
        ]);

        return response()->json(['partner' => $this->publicPartner($partner)], 201);
    }

    public function tools(Request $request): JsonResponse
    {
        $partner = $this->approvedPartner($request);
        $data = $request->validate([
            'to' => ['nullable', 'string', 'max:500'],
            'sub_id' => ['nullable', 'string', 'max:80', 'regex:/^[a-zA-Z0-9_-]+$/'],
        ]);
        $destination = $data['to'] ?? '/';
        if (!str_starts_with($destination, '/') || str_starts_with($destination, '//')
            || str_contains($destination, '\\') || str_contains($destination, '#')) {
            return response()->json(['message' => 'Invalid affiliate destination.'], 422);
        }

        return response()->json([
            'url' => url('/api/v1/affiliate/visit/'.$partner->public_code).'?'.http_build_query(array_filter([
                'to' => $destination,
                'sub_id' => $data['sub_id'] ?? null,
            ], static fn ($value) => $value !== null)),
        ]);
    }

    public function report(Request $request): JsonResponse
    {
        $partner = $this->approvedPartner($request);
        $from = now()->subDays(30)->startOfDay();
        $clicks = DB::table('affiliate_clicks')
            ->where('affiliate_partner_id', $partner->id)->where('created_at', '>=', $from)->count();
        $orders = DB::table('affiliate_order_attributions as attribution')
            ->join('orders', 'orders.id', '=', 'attribution.order_id')
            ->where('attribution.affiliate_partner_id', $partner->id)
            ->where('attribution.created_at', '>=', $from)
            ->selectRaw('orders.status, count(*) as total')
            ->groupBy('orders.status')->pluck('total', 'orders.status');

        return response()->json([
            'period_days' => 30,
            'clicks' => $clicks,
            'orders' => (int) $orders->sum(),
            'paid_orders' => (int) ($orders[1] ?? 0),
            'completed_orders' => (int) ($orders[3] ?? 0),
            'earnings_available' => false,
            'payouts_available' => false,
        ]);
    }

    private function approvedPartner(Request $request): AffiliatePartner
    {
        return AffiliatePartner::query()->where('user_id', $request->user()->id)
            ->where('status', 'approved')->firstOrFail();
    }

    private function publicPartner(AffiliatePartner $partner): array
    {
        return [
            'display_name' => $partner->display_name,
            'website_url' => $partner->website_url,
            'promotion_description' => $partner->promotion_description,
            'status' => $partner->status,
            'created_at' => $partner->created_at?->toIso8601String(),
        ];
    }
}
