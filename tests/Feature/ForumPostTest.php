<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_forum_post_detail_page_is_available(): void
    {
        $post = Post::create([
            'title' => '落とし物の詳細テスト',
            'content' => '詳細ページの確認用です。',
            'category' => '落とし物',
            'posted_by' => '匿名',
            'published_at' => now(),
        ]);

        $response = $this->get(route('forum.show', $post));

        $response->assertStatus(200);
        $response->assertSee('落とし物の詳細テスト');
        $response->assertSee('詳細ページの確認用です。');
    }

    public function test_authenticated_user_can_edit_and_delete_their_post(): void
    {
        $user = User::factory()->create([
            'login_id' => 'student01',
            'role' => 'student',
        ]);

        $post = Post::create([
            'title' => '元のタイトル',
            'content' => '元の本文',
            'category' => 'サークル',
            'posted_by' => $user->login_id,
            'user_id' => $user->id,
            'published_at' => now(),
        ]);

        $this->actingAs($user);

        $this->get(route('forum.edit', $post))
            ->assertStatus(200)
            ->assertSee('元のタイトル');

        $this->patch(route('forum.update', $post), [
            'category' => '教科書',
            'title' => '更新後のタイトル',
            'content' => '更新後の本文',
        ])->assertRedirect(route('forum.show', $post));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => '更新後のタイトル',
            'content' => '更新後の本文',
            'category' => '教科書',
        ]);

        $this->delete(route('forum.destroy', $post))
            ->assertRedirect(route('forum.top'));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
