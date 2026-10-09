<?php

namespace App\Http\Controllers;

use App\Models\AffiliatePartner;
use App\Models\Product;
use App\Support\MarketplacePricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AffiliateCatalogController extends Controller
{
    public function __invoke(Request $request, MarketplacePricing $pricing): JsonResponse
    {
        AffiliatePartner::query()->where('user_id', $request->user()->id)
            ->where('status', 'approved')->firstOrFail();

        $data = $request->validate([
            'city' => ['nullable', 'string', 'max:100'],
            'duration_days' => ['nullable', 'integer', 'min:1', 'max:31'],
            'page' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);
        $days = (int) ($data['duration_days'] ?? 3);
        $query = Product::query()->select([
            'id', 'brand_id', 'model_id', 'model_year', 'price',
            'duration_prices', 'currency_code', 'photos',
        ])->listed()->where('type_id', 4)->with(['brand', 'model', 'sales']);

        if (isset($data['city'])) {
            $city = trim($data['city']);
            if ($city !== '') {
                $escaped = addcslashes($city, '%_\\');
                $query->where('location', 'ILIKE', '%'.$escaped.'%');
            }
        }

        $products = $query->orderByDesc('created_at')->paginate(24);
        $items = $products->getCollection()->map(function (Product $product) use ($pricing, $days): array {
            $supplierPrice = $product->supplierPriceForDays($days);
            $sale = $product->sales->first()?->price;

            return [
                'id' => $product->id,
                'brand' => $product->brand?->name,
                'model' => $product->model?->name,
                'year' => $product->model_year,
                'daily_price' => $pricing->customerAmount($supplierPrice),
                'sale_daily_price' => is_numeric($sale) && $sale > 0 && $sale < $supplierPrice
                    ? $pricing->customerAmount((float) $sale) : null,
                'currency' => $product->currency_code,
                'photos' => array_slice(array_values(array_filter(
                    (array) $product->photos,
                    static fn ($url) => is_string($url)
                        && in_array(parse_url($url, PHP_URL_HOST), ['cheapcarent.com', 'www.cheapcarent.com', 'api.cheapcarent.com'], true)
                )), 0, 3),
                'availability_unchecked' => true,
            ];
        })->values();

        return response()->json([
            'data' => $items,
            'pagination' => [
                'page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }
}
