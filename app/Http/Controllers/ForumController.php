<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

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

        $posts = $query->orderByDesc('published_at')->get();

        return view('forum.forum-top', compact('posts', 'categories', 'selectedCategory', 'searchQuery'));
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
            'posted_by' => '匿名',
            'image_url' => null,
            'published_at' => now(),
        ]);

        if ($request->hasFile('media')) {
            $files = $request->file('media');
            if (is_array($files) && count($files) > 0) {
                $firstFile = $files[0];
                $path = $firstFile->store('forum_media', 'public');
                $post->update(['image_url' => asset('storage/' . $path)]);
            }
        }

        return redirect()->route('forum.top');
    }
}
