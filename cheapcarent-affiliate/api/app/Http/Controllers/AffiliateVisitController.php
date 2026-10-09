<?php

namespace App\Http\Controllers;

use App\Models\AffiliatePartner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliateVisitController extends Controller
{
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $partner = AffiliatePartner::query()
            ->where('public_code', $code)
            ->where('status', 'approved')
            ->firstOrFail();

        $data = $request->validate([
            'to' => ['nullable', 'string', 'max:500'],
            'sub_id' => ['nullable', 'string', 'max:80', 'regex:/^[a-zA-Z0-9_-]+$/'],
        ]);
        $destination = $data['to'] ?? '/';
        if (!str_starts_with($destination, '/') || str_starts_with($destination, '//')
            || str_contains($destination, '\\') || str_contains($destination, '#')) {
            abort(422, 'Invalid affiliate destination.');
        }

        // Count one visit per partner, destination and browser fingerprint per
        // half-hour window. Store only a keyed digest, never the raw IP/UA.
        $dedupeKey = hash_hmac('sha256', implode('|', [
            $partner->id,
            $destination,
            $request->ip(),
            substr((string) $request->userAgent(), 0, 255),
            intdiv(now()->timestamp, 1800),
        ]), config('app.key'));
        $newClickId = (string) Str::uuid();
        DB::table('affiliate_clicks')->insertOrIgnore([
            'id' => $newClickId,
            'affiliate_partner_id' => $partner->id,
            'destination' => $destination,
            'sub_id' => $data['sub_id'] ?? null,
            'dedupe_key' => $dedupeKey,
            'created_at' => now(),
        ]);
        $clickId = DB::table('affiliate_clicks')->where('dedupe_key', $dedupeKey)->value('id');
        if (!$clickId) {
            abort(503, 'Affiliate tracking is temporarily unavailable.');
        }

        $separator = str_contains($destination, '?') ? '&' : '?';
        return redirect()->away('https://cheapcarent.com'.$destination.$separator.'affiliate_click_id='.$clickId, 302)
            ->header('Cache-Control', 'no-store, private');
    }
}
