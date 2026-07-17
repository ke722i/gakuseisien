<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $categories = ['すべて', '落とし物', 'サークル', '教科書'];
        $selectedCategory = $request->query('category', 'すべて');
        $searchQuery = $request->query('q');

        $query = Post::query();

        if ($selectedCategory !== 'すべて') {
            $query->where('category', $selectedCategory);
        }

        if ($searchQuery) {
            $query->where(function ($subQuery) use ($searchQuery) {
                $subQuery->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('content', 'like', "%{$searchQuery}%")
                    ->orWhere('posted_by', 'like', "%{$searchQuery}%");
            });
        }

        $posts = $query->orderByDesc('published_at')->paginate(10);
        $posts->appends(array_filter([
            'category' => $selectedCategory !== 'すべて' ? $selectedCategory : null,
            'q' => $searchQuery,
        ], fn ($value) => $value !== null));

        return view('forum.forum-top', compact('posts', 'categories', 'selectedCategory', 'searchQuery'));
    }

    public function show(Post $post)
    {
        $post->load(['replies.user']);

        // 同じカテゴリの関連投稿を取得（自分の投稿を除く、最新5件）
        $relatedPosts = Post::where('category', $post->category)
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return view('forum.show', compact('post', 'relatedPosts'));
    }

    public function storeReply(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $reply = PostReply::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'author_name' => Auth::check() ? Auth::user()->login_id : '匿名',
            'content' => $validated['content'],
        ]);

        $post->increment('reply_count');
        $post->update(['last_replied_at' => $reply->created_at]);

        return redirect()->route('forum.show', $post);
    }

    public function edit(Post $post)
    {
        // 投稿者本人以外は編集画面を開けない（user_idがnullの匿名投稿も編集不可）
        abort_if($post->user_id === null || $post->user_id !== Auth::id(), 403);

        return view('forum.edit', compact('post'));
    }

    public function create()
    {
        return view('forum.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:300',
            'content' => 'nullable|string',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240',
        ]);

        $post = Post::create([
            'category' => $validated['category'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'posted_by' => Auth::user()?->login_id ?? '匿名',
            'user_id' => Auth::id(),
            'image_url' => null,
            'published_at' => now(),
        ]);

        if ($request->hasFile('media')) {
            $media = $request->file('media');
            
            // 単一ファイルまたは配列対応
            if (!is_array($media)) {
                $media = [$media];
            }
            
            if (count($media) > 0 && $media[0] !== null) {
                $path = $media[0]->store('forum_media', 'public');
                if ($path) {
                    $post->update(['image_url' => asset('storage/' . $path)]);
                }
            }
        }

        return redirect()->route('forum.top');
    }

    public function update(Request $request, Post $post)
    {
        // 投稿者本人以外は更新できない
        abort_if($post->user_id === null || $post->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:300',
            'content' => 'nullable|string',
        ]);

        $post->update([
            'category' => $validated['category'],
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
        ]);

        return redirect()->route('forum.show', $post);
    }

    public function destroy(Post $post)
    {
        // 投稿者本人以外は削除できない
        abort_if($post->user_id === null || $post->user_id !== Auth::id(), 403);

        $post->delete();

        return redirect()->route('forum.top');
    }
}
