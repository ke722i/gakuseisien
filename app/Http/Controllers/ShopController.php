<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleMapsService;

class ShopController extends Controller
{
    /**
     * 店舗申請・店舗更新で共通のバリデーションルール。
     * 決済方法は画面上チェックボックスの複数選択のため配列で受け取る。
     */
    private function shopValidationRules(): array
    {
        return [
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
        ];
    }

    // ホーム
    public function index(Request $request)
    {
        $onlyFavorites = $request->boolean('favorites');

        $shops = Shop::where('is_visible', true)
            // お気に入りのみ表示（ログイン時のみ有効）
            ->when($onlyFavorites && Auth::check(), function ($query) {
                $query->whereHas('favoritedBy', fn ($q) => $q->where('users.id', Auth::id()));
            })
            ->with('favoritedBy')
            ->paginate(9)
            ->withQueryString();

        return view('store.shome', compact('shops', 'onlyFavorites'));
    }

    /**
     * お気に入りの登録・解除（トグル）。
     * 非同期（fetch）からもフォーム送信からも呼べるようにする。
     */
    public function toggleFavorite(Request $request, Shop $shop)
    {
        $user = Auth::user();

        // detach は削除件数を返すので、0 なら未登録だったとみなして attach する
        $removed = $user->favoriteShops()->detach($shop->id);

        if ($removed === 0) {
            $user->favoriteShops()->attach($shop->id);
            $favorited = true;
        } else {
            $favorited = false;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'favorited' => $favorited,
                'count' => $shop->favoritedBy()->count(),
            ]);
        }

        return back()->with('success', $favorited ? 'お気に入りに追加しました。' : 'お気に入りを解除しました。');
    }

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
        // キー未設定の環境では案内文とオートコンプリートを出さず、手入力のみにする
        $placeSearchEnabled = !empty(config('services.google_maps.server_key'));

        $distanceAutoFillEnabled = $placeSearchEnabled
            && config('services.school.latitude') !== null
            && config('services.school.latitude') !== ''
            && config('services.school.longitude') !== null
            && config('services.school.longitude') !== '';

        return view('store.request', compact(
            'placeSearchEnabled',
            'distanceAutoFillEnabled'
        ));
    }

    /**
     * 申請フォームの店舗名オートコンプリート用。
     * 入力中のキーワードでGoogle Placesを検索し、各入力欄に流し込む値を返す。
     */
    public function placeSearch(Request $request, GoogleMapsService $googleMapsService)
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'max:100'],
        ]);

        return response()->json([
            'candidates' => $googleMapsService->searchPlaceCandidates($validated['keyword']),
        ]);
    }

    // 店舗申請保存
    public function storeRequest(Request $request)
    {
        $validated = $request->validate($this->shopValidationRules());

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
        $validated = $request->validate($this->shopValidationRules());

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

            // 決済方法（配列カラムのため部分一致で判定する）
            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('payment_method', 'like', "%{$paymentMethod}%");
            })

            ->with('favoritedBy')
            // 検索条件をページ移動後も維持する
            ->paginate(9)
            ->withQueryString();

        $onlyFavorites = false;

        return view('store.shome', compact('shops', 'onlyFavorites'));
    }

    public function admin()
    {
        $requests = ShopRequest::orderBy('created_at', 'desc')->get();
        $shops = Shop::orderBy('created_at', 'desc')->get();

        return view('store.admin', compact('requests', 'shops'));
    }
}
