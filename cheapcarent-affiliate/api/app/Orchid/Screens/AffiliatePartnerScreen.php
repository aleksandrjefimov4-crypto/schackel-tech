<?php

namespace App\Orchid\Screens;

use App\Models\AffiliatePartner;
use App\Orchid\Layouts\AffiliatePartnerListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class AffiliatePartnerScreen extends Screen
{
    use Concerns\RequiresAdminSectionPermission;

    public function query(): iterable
    {
        return ['affiliates' => AffiliatePartner::query()->latest()->paginate(25)];
    }

    public function name(): ?string
    {
        return 'Affiliate applications';
    }

    public function description(): ?string
    {
        return 'Review applicants. Affiliate links and basic tracking are available after approval; payouts remain disabled.';
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {
        return [AffiliatePartnerListLayout::class];
    }

    public function approve(Request $request): void
    {
        $partner = AffiliatePartner::query()->findOrFail($request->integer('id'));
        if ($partner->status !== 'pending') {
            return;
        }

        $partner->update(['status' => 'approved', 'approved_at' => now()]);
        Toast::success('Affiliate application approved. Links and basic tracking are enabled.');
    }

    public function reject(Request $request): void
    {
        $partner = AffiliatePartner::query()->findOrFail($request->integer('id'));
        if ($partner->status !== 'pending') {
            return;
        }

        $partner->update(['status' => 'rejected']);
        Toast::info('Affiliate application declined.');
    }
}
