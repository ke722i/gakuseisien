# データベース運用ガイド（チーム開発用）

Laravel + PostgreSQL でのチーム開発における、データベースの扱い方とマイグレーションの書き方をまとめたドキュメントです。

- **バックエンド**: Laravel 13 / PHP 8.4
- **データベース**: PostgreSQL 18（ローカル・本番で統一）
- **本番環境**: Render

---

## 1. 基本の考え方：DB本体は共有しない

チーム開発では、**各自が自分のPCに自分専用のPostgreSQLを持ちます**。みんなで1つのDBにつなぐわけではありません。

では、テーブル構成（スキーマ）をどうやって全員で揃えるのか？
→ **「DBの中身」ではなく「DBの設計図（マイグレーションファイル）」をGitで共有する** のがポイントです。

```
開発者A：マイグレーション作成
      │ git push
      ▼
   GitHub（マイグレーションを共有）
      │ git pull
      ├──────────────┬──────────────┐
      ▼              ▼              ▼
  開発者B         開発者C        Render 本番
 （自分のDBに反映）（自分のDBに反映）（デプロイ時に反映）
```

各自のDBは直接つながっていないので、誰かの操作で他人のデータが壊れる心配はありません。

---

## 2. マイグレーションとは

テーブルの設計（列を追加する、テーブルを作る等）を **PHPで書くファイル** です。
「DBの設計図」であると同時に「変更履歴」の役割も持ちます。

- ファイルは `database/migrations/` に置かれる
- ファイル名の先頭に日時が付き、**実行順が保証される**
- 一度チームで共有したマイグレーションは、原則あとから書き換えない（新しいマイグレーションを追加して変更する）

Laravelでは、データの読み書きも **Eloquent（モデル）** を使うため、生のSQLを書く場面はほとんどありません。全員が同じお作法で扱えるので、事故が起きにくいのが強みです。

---

## 3. マイグレーションの書き方

### 3-1. ファイルを作る

```bash
# 新しいテーブルを作る場合
php artisan make:migration create_posts_table

# 既存テーブルにカラムを追加する場合
php artisan make:migration add_slug_to_posts_table
```

`database/migrations/` に `2026_07_03_120000_create_posts_table.php` のようなファイルが生成されます。

### 3-2. 基本構造（up と down）

すべてのマイグレーションは `up()` と `down()` の2つのメソッドを持ちます。

- `up()` … マイグレーションを **適用** するときの処理（テーブル作成など）
- `down()` … マイグレーションを **取り消す** ときの処理（テーブル削除など）

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ここに適用時の処理を書く
    }

    public function down(): void
    {
        // ここに取り消し時の処理を書く
    }
};
```

### 3-3. テーブルを新規作成する

```php
public function up(): void
{
    Schema::create('posts', function (Blueprint $table) {
        $table->id();                                        // 主キー（自動採番）
        $table->foreignId('user_id')->constrained()          // usersテーブルへの外部キー
              ->cascadeOnDelete();                           // 親が消えたら一緒に削除
        $table->string('title');                             // 可変長文字列（VARCHAR）
        $table->text('body');                                // 長文（TEXT）
        $table->boolean('is_published')->default(false);     // 真偽値・初期値false
        $table->timestamps();                                // created_at / updated_at を自動追加
    });
}

public function down(): void
{
    Schema::dropIfExists('posts');
}
```

### 3-4. 既存テーブルにカラムを追加する

`Schema::create` ではなく `Schema::table` を使います。

```php
public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('slug')->nullable()->after('title');  // titleの後ろにslugを追加
    });
}

public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('slug');
    });
}
```

### 3-5. よく使うカラム型

| 書き方 | 用途 |
|---|---|
| `$table->id()` | 主キー（自動採番のBIGINT） |
| `$table->string('name')` | 短めの文字列（VARCHAR、デフォルト255文字） |
| `$table->text('body')` | 長い文章 |
| `$table->integer('count')` | 整数 |
| `$table->boolean('is_active')` | 真偽値（true/false） |
| `$table->date('birthday')` | 日付 |
| `$table->dateTime('published_at')` | 日時 |
| `$table->decimal('price', 8, 2)` | 小数（金額など） |
| `$table->json('options')` | JSONデータ |
| `$table->foreignId('user_id')` | 外部キー用の列 |
| `$table->timestamps()` | created_at / updated_at をまとめて追加 |
| `$table->softDeletes()` | 論理削除用の deleted_at を追加 |

### 3-6. よく使う修飾子

カラムのうしろに繋げて、性質を指定できます。

| 書き方 | 意味 |
|---|---|
| `->nullable()` | 空（NULL）を許可する |
| `->default(値)` | 初期値を設定する |
| `->unique()` | 重複を許さない |
| `->after('カラム名')` | 指定カラムの後ろに追加する |
| `->constrained()` | 外部キー制約をつける |
| `->cascadeOnDelete()` | 親が削除されたら子も削除する |

### 3-7. 反映する

書き終えたら、DBに反映します。

```bash
php artisan migrate
```

---

## 4. データの読み書き（Eloquent）

テーブルを作ったら、対応する「モデル」を通してデータを操作します。生SQLは不要です。

```php
use App\Models\Post;

// 作成
Post::create([
    'user_id' => 1,
    'title'   => '初めての投稿',
    'body'    => '本文です',
]);

// 全件取得
$posts = Post::all();

// 条件で取得
$published = Post::where('is_published', true)->get();

// 1件取得
$post = Post::find(1);

// 更新
$post->update(['title' => '修正後のタイトル']);

// 削除
$post->delete();
```

モデルは次のコマンドで作れます（マイグレーションも同時に作る場合は `-m`）。

```bash
php artisan make:model Post -m
```

---

## 5. チームで守るルール

事故を防ぐ鍵はこの4つです。

1. **テーブルの変更は必ずマイグレーションで行う。**
   pgAdmin で手動でテーブルをいじらない。手動変更はGitに残らず、他の人のDBに反映されないため「自分だけ動く」の原因になる。

2. **`.env` はGitに上げない。**
   DBパスワードなど各自バラバラな設定。`.gitignore` に含まれている（Laravelは最初からそう設定済み）。共有すべき項目は `.env.example` に見本として書く。

3. **pull したら `php artisan migrate` を習慣にする。**
   誰かがテーブルを変えたとき、これを実行しないと自分のDBが古いままになる。

4. **共有したいテストデータはシーダーで用意する。**
   手で入れたデータは自分のPCにしか無い。`database/seeders/` にシーダーを書き、`php artisan db:seed` で全員が同じ初期データを入れられるようにする。

---

## 6. よく使うコマンド一覧

```bash
php artisan make:migration create_xxx_table  # テーブル作成用の設計図を作る
php artisan make:model Post -m                # モデルとマイグレーションを同時に作る
php artisan migrate                           # 未反映のマイグレーションをDBに反映
php artisan migrate:status                    # どこまで反映済みか確認
php artisan migrate:rollback                  # 直前のマイグレーションを取り消す
php artisan db:seed                           # 共有のテストデータを投入
php artisan migrate:fresh --seed              # DBを作り直して初期データ投入
```

> ⚠️ **注意**: `migrate:fresh` は全テーブルを削除して作り直します。
> 開発中の自分のDBには便利ですが、**本番（Render）では絶対に使わないでください。**

---

## 7. トラブル対処

### マイグレーションが衝突したとき

複数人が同時期にマイグレーションを追加すると、実行順やカラムの重複で問題が起きることがあります。

- 基本方針：**共有済みのマイグレーションは書き換えず、新しいマイグレーションを追加して修正する**
- 自分のローカルDBだけリセットしたいとき：`php artisan migrate:fresh --seed`
- 「どこまで反映されているか分からない」とき：`php artisan migrate:status` で確認

### 「自分だけ動かない / 自分だけ動く」とき

たいていは **migrate のし忘れ** か、**手動でDBをいじったこと** が原因です。

1. `git pull` して最新のマイグレーションを取得
2. `php artisan migrate` で反映
3. それでもおかしければ `php artisan migrate:fresh --seed` でDBを作り直す（※ローカルのみ）

---

## 参考リンク

- Laravel マイグレーション公式ドキュメント: https://readouble.com/laravel/13.x/ja/migrations.html
- Laravel Eloquent 公式ドキュメント: https://readouble.com/laravel/13.x/ja/eloquent.html
