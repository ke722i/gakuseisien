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

    public function test_guest_cannot_edit_or_delete_post(): void
    {
        $owner = User::factory()->create([
            'login_id' => 'owner001',
            'role' => 'student',
        ]);

        $post = Post::create([
            'title' => '他人の投稿',
            'content' => '本文',
            'category' => 'サークル',
            'posted_by' => $owner->login_id,
            'user_id' => $owner->id,
            'published_at' => now(),
        ]);

        // 未ログインはログイン画面へリダイレクトされる
        $this->get(route('forum.edit', $post))->assertRedirect(route('login'));
        $this->patch(route('forum.update', $post), [
            'category' => '教科書',
            'title' => '改ざん',
        ])->assertRedirect(route('login'));
        $this->delete(route('forum.destroy', $post))->assertRedirect(route('login'));

        // データは変わっていない
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => '他人の投稿']);
    }

    public function test_other_user_cannot_edit_or_delete_post(): void
    {
        $owner = User::factory()->create([
            'login_id' => 'owner002',
            'role' => 'student',
        ]);
        $attacker = User::factory()->create([
            'login_id' => 'attacker1',
            'role' => 'student',
        ]);

        $post = Post::create([
            'title' => '他人の投稿',
            'content' => '本文',
            'category' => 'サークル',
            'posted_by' => $owner->login_id,
            'user_id' => $owner->id,
            'published_at' => now(),
        ]);

        $this->actingAs($attacker);

        // 本人以外は403
        $this->get(route('forum.edit', $post))->assertStatus(403);
        $this->patch(route('forum.update', $post), [
            'category' => '教科書',
            'title' => '改ざん',
        ])->assertStatus(403);
        $this->delete(route('forum.destroy', $post))->assertStatus(403);

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => '他人の投稿']);
    }

    public function test_anonymous_post_cannot_be_edited_by_anyone(): void
    {
        $user = User::factory()->create([
            'login_id' => 'someone01',
            'role' => 'student',
        ]);

        // user_id が null の匿名投稿
        $post = Post::create([
            'title' => '匿名投稿',
            'category' => '落とし物',
            'posted_by' => '匿名',
            'published_at' => now(),
        ]);

        $this->actingAs($user);

        $this->get(route('forum.edit', $post))->assertStatus(403);
        $this->delete(route('forum.destroy', $post))->assertStatus(403);
    }
}
