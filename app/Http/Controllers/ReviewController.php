<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * 口コミ投稿
     */
    public function store(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $editToken = Str::random(64);

        $review = Review::create([
            'shop_id' => $shop->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'edit_token' => $editToken,
            'is_visible' => true,
        ]);

        $editableReviews = session()->get('editable_reviews', []);

        $editableReviews[$review->id] = $editToken;

        session()->put('editable_reviews', $editableReviews);

        return redirect()
            ->route('store.more', $shop->id)
            ->with('success', '口コミを投稿しました。');
    }

    /**
     * 口コミ編集画面
     */
    public function edit(Review $review)
    {
        $this->authorizeReviewOwner($review);

        return view('store.review_edit', compact('review'));
    }

    /**
     * 口コミ更新
     */
    public function update(Request $request, Review $review)
    {
        $this->authorizeReviewOwner($review);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()
            ->route('store.more', $review->shop_id)
            ->with('success', '口コミを更新しました。');
    }

    /**
     * 口コミ削除
     */
    public function destroy(Review $review)
    {
        $this->authorizeReviewOwner($review);

        $shopId = $review->shop_id;
        $reviewId = $review->id;

        $review->delete();

        $editableReviews = session()->get('editable_reviews', []);

        unset($editableReviews[$reviewId]);

        session()->put('editable_reviews', $editableReviews);

        return redirect()
            ->route('store.more', $shopId)
            ->with('success', '口コミを削除しました。');
    }

    /**
     * 投稿者本人か確認
     */
    private function authorizeReviewOwner(Review $review): void
    {
        $editableReviews = session()->get('editable_reviews', []);

        $sessionToken = $editableReviews[$review->id] ?? null;

        if (
            !$sessionToken ||
            !$review->edit_token ||
            !hash_equals($review->edit_token, $sessionToken)
        ) {
            abort(403, 'この口コミを編集または削除する権限がありません。');
        }
    }
}