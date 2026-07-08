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
}
