<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopRequest;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    // ホーム
    public function index()
    {
        $shops = Shop::where('is_visible', true)->get();

        return view('store.home', compact('shops'));
    }

    // 詳細
    public function show($id)
    {
        $shop = Shop::findOrFail($id);

        return view('store.more', compact('shop'));
    }

    // 申請画面
    public function request()
    {
        return view('store.request');
    }

    // 管理画面
    public function admin()
    {
        $shops = Shop::all();
        $requests = ShopRequest::all();

        return view('store.admin', compact('shops', 'requests'));
    }

    // ★申請保存
    public function storeRequest(Request $request)
    {
        // shop_requests テーブルのNOT NULL制約に合わせて全項目必須にする
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'business_hours' => ['required', 'string', 'max:255'],
            'budget' => ['required', 'integer', 'min:0'],
            'distance' => ['required', 'integer', 'min:0'],
            'payment_method' => ['required', 'array', 'min:1'],
            'payment_method.*' => ['string'],
        ], [], [
            'name' => '店舗名',
            'genre' => 'ジャンル',
            'address' => '住所',
            'business_hours' => '営業時間',
            'budget' => '平均価格',
            'distance' => '学校からの距離',
            'payment_method' => '決済方法',
        ]);

        ShopRequest::create([
            'name' => $validated['name'],
            'genre' => $validated['genre'],
            'address' => $validated['address'],
            'business_hours' => $validated['business_hours'],
            'budget' => $validated['budget'],
            'distance' => $validated['distance'],
            // チェックボックス（配列）を「現金・PayPay」の形の文字列にして保存
            'payment_method' => implode('・', $validated['payment_method']),
            'status' => 'pending',
        ]);

        return redirect()->route('store.request')
            ->with('success', '申請しました。');
    }
}