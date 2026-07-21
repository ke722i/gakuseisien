<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopRequest;
use Illuminate\Http\Request;
use App\Services\GoogleMapsService;

class ShopController extends Controller
{
    // ホーム
    public function index()
{
    $shops = Shop::where('is_visible', true)
        ->paginate(9)
        ->withQueryString();

    return view('store.shome', compact('shops'));
}

    // 店舗詳細
// 店舗詳細
// 店舗詳細
public function show($id, GoogleMapsService $googleMapsService)
{
    $shop = Shop::with([
        'reviews' => function ($query) {
            $query->where('is_visible', true)
                ->latest();
        },
    ])->findOrFail($id);

    // 学生支援.com側の評価
    $reviewCount = $shop->reviews->count();
    $studentRating = $shop->reviews->avg('rating');

    // Google評価取得用
    $googlePlacesEnabled =
        !empty(config('services.google_maps.server_key'));

    // 地図埋め込み用
    $googleEmbedEnabled =
        !empty(config('services.google_maps.embed_key'));

    // Google Places APIから店舗情報を取得
    $googlePlace = null;

    if ($googlePlacesEnabled) {
        $googlePlace = $googleMapsService->findPlace(
            $shop->name,
            $shop->address
        );
    }

    // Google評価
    $googleRating = $googlePlace['rating'] ?? null;

    // Google評価件数
    $googleReviewCount =
        $googlePlace['userRatingCount'] ?? null;

    // Google Mapsへのリンク
    $googleMapsUrl =
        $googlePlace['googleMapsUri']
        ?? 'https://www.google.com/maps/search/?api=1&query='
        . urlencode($shop->name . ' ' . $shop->address);

    // 埋め込み地図URL
    $mapEmbedUrl = null;

    if ($googleEmbedEnabled) {
        $mapQuery = !empty($googlePlace['id'])
            ? 'place_id:' . $googlePlace['id']
            : $shop->name . ' ' . $shop->address;

        $mapEmbedUrl =
            'https://www.google.com/maps/embed/v1/place?'
            . http_build_query([
                'key' => config('services.google_maps.embed_key'),
                'q' => $mapQuery,
                'language' => 'ja',
                'region' => 'jp',
            ]);
    }

    return view('store.more', compact(
        'shop',
        'reviewCount',
        'studentRating',
        'googlePlacesEnabled',
        'googleEmbedEnabled',
        'googleRating',
        'googleReviewCount',
        'googleMapsUrl',
        'mapEmbedUrl'
    ));
}

    // 申請画面
    public function request()
    {
        return view('store.request');
    }

    // 管理画面
   public function storeRequest(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'genre' => ['required', 'string', 'max:100'],
        'address' => ['required', 'string', 'max:255'],
        'business_hours' => ['required', 'string', 'max:255'],
        'budget' => ['required', 'integer', 'min:0'],
        'distance' => ['required', 'integer', 'min:0'],

        'payment_method' => ['required', 'array', 'min:1'],
        'payment_method.*' => [
            'string',
            'in:現金,クレジットカード,交通系IC,QRコード決済,電子マネー',
        ],

        'official_url' => ['nullable', 'url', 'max:255'],
    ]);

    ShopRequest::create($validated);

    return redirect()
        ->route('store.request')
        ->with('success', '店舗情報を申請しました。');
}

    // 申請詳細画面
    public function requestMore($id)
    {
        $shopRequest = ShopRequest::findOrFail($id);

        return view('store.request_more', compact('shopRequest'));
    }

    // 申請承認
    public function approve($id)
    {
        $shopRequest = ShopRequest::findOrFail($id);

        Shop::create([
            'name' => $shopRequest->name,
            'genre' => $shopRequest->genre,
            'address' => $shopRequest->address,
            'business_hours' => $shopRequest->business_hours,
            'budget' => $shopRequest->budget,
            'distance' => $shopRequest->distance,
            'payment_method' => $shopRequest->payment_method,
            'official_url' => $shopRequest->official_url,
            'is_visible' => true,
        ]);

        $shopRequest->delete();

        return redirect()
            ->route('store.admin')
            ->with('success', '承認しました。');
    }

    // 申請却下
    public function reject($id)
    {
        $shopRequest = ShopRequest::findOrFail($id);

        $shopRequest->delete();

        return redirect()
            ->route('store.admin')
            ->with('success', '却下しました。');
    }

    // 編集画面
    public function edit($id)
    {
        $shop = Shop::findOrFail($id);

        return view('store.edit', compact('shop'));
    }

    // 店舗情報更新
    public function update(Request $request, Shop $shop)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'genre' => ['required', 'string', 'max:100'],
        'address' => ['required', 'string', 'max:255'],
        'business_hours' => ['required', 'string', 'max:255'],
        'budget' => ['required', 'integer', 'min:0'],
        'distance' => ['required', 'integer', 'min:0'],

        'payment_method' => ['required', 'array', 'min:1'],
        'payment_method.*' => [
            'string',
            'in:現金,クレジットカード,交通系IC,QRコード決済,電子マネー',
        ],

        'official_url' => ['nullable', 'url', 'max:255'],
    ]);

    $shop->update($validated);

    return redirect()
        ->route('store.admin')
        ->with('success', '店舗情報を更新しました。');
}
    // 表示・非表示切り替え
    public function hide($id)
    {
        $shop = Shop::findOrFail($id);

        $shop->update([
            'is_visible' => !$shop->is_visible,
        ]);

        return redirect()
            ->route('store.admin')
            ->with('success', '表示状態を変更しました。');
    }

    // 店舗削除
    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);

        $shop->delete();

        return redirect()
            ->route('store.admin')
            ->with('success', '店舗を削除しました。');
    }

    // 検索・フィルター
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $genre = $request->input('genre');
        $budget = $request->input('budget');
        $distance = $request->input('distance');
        $paymentMethod = $request->input('payment_method');

        $shops = Shop::where('is_visible', true)
            // 店舗名・住所検索
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($subQuery) use ($keyword) {
                    $subQuery
                        ->where('name', 'like', "%{$keyword}%")
                        ->orWhere('address', 'like', "%{$keyword}%");
                });
            })

            // ジャンル
            ->when($genre, function ($query) use ($genre) {
                $query->where('genre', $genre);
            })

            // 予算以下
            ->when($budget !== null && $budget !== '', function ($query) use ($budget) {
                $query->where('budget', '<=', $budget);
            })

            // 距離以下
            ->when($distance !== null && $distance !== '', function ($query) use ($distance) {
                $query->where('distance', '<=', $distance);
            })

            // 決済方法
            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })

            ->paginate(9)
            ->withQueryString();

        return view('store.shome', compact('shops'));
    }
    public function admin()
{
    $requests = ShopRequest::orderBy('created_at', 'desc')->get();
    $shops = Shop::orderBy('created_at', 'desc')->get();

    return view('store.admin', compact('requests', 'shops'));
}
}
