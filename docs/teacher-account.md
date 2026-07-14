# 先生アカウントについて

役割（先生／学生）によって表示や遷移先が変わる機能があるため、動作確認用のアカウントを用意しています。

## アカウント一覧

| 種別 | ログインID | パスワード | role |
| --- | --- | --- | --- |
| 先生 | `Teacher` | `123456` | `teacher` |
| 先生 | `teacher01` | `teacher1` | `teacher` |
| 学生 | `student01` | `student1` | `student` |

> ⚠️ `Teacher` のパスワード `123456` は数字のみで、**新規登録フォームのルール（英字＋数字必須）には通りません**。
> これは DB に直接登録した動作確認用アカウントで、ログイン時は ID/パスワードの一致のみを確認するためログインできます。

## 役割（role）の仕組み

- `users` テーブルの `role` カラムで判定します（`'teacher'` / `'student'`）。
- モデルに判定用メソッドがあります: `App\Models\User::isTeacher()`（`role === 'teacher'` を返す）。

## 役割で挙動が変わる箇所

| 画面 | 先生 | 学生（未ログイン含む） |
| --- | --- | --- |
| 空き教室予約（`/classroom-reservation`） | `reservation.home.teacher` | `reservation.home.student` |

判定は `routes/web.php` で `Auth::user()?->isTeacher()` を使って出し分けています。今後、役割で分けたい画面が増えたら同じパターンで追加できます。

## サイドバーのID表示

ログイン中は、サイドバーのログアウトボタンの上に現在の `login_id`（👤 ID名）が表示されます（`resources/views/partials/sidebar.blade.php`）。

## 自分のDBにアカウントを用意する方法

シーダーに登録済みなので、各自のPCで次を実行すると3アカウントが作成されます（既存があればスキップ）。

```bash
php artisan db:seed
```

- 定義場所: `database/seeders/DatabaseSeeder.php`
- `firstOrCreate` を使っているため、**何度実行しても重複しません**。
- 個別に1件だけ追加したい場合は `php artisan tinker` で:

  ```php
  App\Models\User::firstOrCreate(
      ['login_id' => 'Teacher'],
      ['password' => Illuminate\Support\Facades\Hash::make('123456'), 'role' => 'teacher'],
  );
  ```
