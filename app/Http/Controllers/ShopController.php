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
        ShopRequest::create([
            'name' => $request->name,
            'genre' => $request->genre,
            'address' => $request->address,
            'business_hours' => $request->business_hours,
            'budget' => $request->budget,
            'distance' => $request->distance,
            'payment_method' => $request->payment_method,
            'applicant' => $request->applicant,
            'status' => 'pending',
        ]);

        return redirect()->route('store.request')
            ->with('success', '申請しました。');
    }
}