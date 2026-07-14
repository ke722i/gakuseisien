# develop をマージした後にやること（メンバー手順書）

`develop` を自分のブランチに取り込んだ（マージ／プル）あと、**各自のPCで**行う作業をまとめたものです。
Git で来るのは「ファイル」だけで、**DB やリンクは自動では反映されません**。下記を上から順に実行してください。

> ⚠️ すべて `gakuseisien` フォルダ直下のターミナルで実行します（VS Code のターミナル等）。
> サーバー（`start-dev.cmd`）は一旦止めてから実行するのがおすすめです。

---

## 手順（毎回のマージ後）

### ① 依存パッケージの更新（`composer.json` / `package.json` が変わっていた場合）

```bash
composer install
npm install --ignore-scripts
```

- 変わっていなければスキップして構いません。判断が面倒なら毎回実行してもOK（変化なしなら何も起きません）。

### ② データベースへテーブルを反映

```bash
php artisan migrate
```

- 新しいマイグレーション（例：`posts` テーブル）を自分のDBに作成します。
- **安全**です。未実行(Pending)のものだけ追加され、既存データは消えません。
- 実行前に確認したい場合：

  ```bash
  php artisan migrate:status
  ```

  `Pending` があれば `migrate` が必要、すべて `Ran` なら不要です。

### ③ 画像表示用のリンクを作成（初回のみ・1回だけ）

```bash
php artisan storage:link
```

- 投稿画像などアップロードファイルを表示するために必要です（`public/storage → storage/app/public`）。
- **一度作れば以降は不要**です（作成済みかは `public/storage` の有無で判断）。
- Windows で権限エラーになる場合は「開発者モードを有効化」するか、管理者ターミナルで実行してください。

### ④ 起動して確認

```
start-dev.cmd
```

`http://127.0.0.1:8000` が開きます。ログイン後、目的の画面を確認してください。

---

## よくあるエラーと対処

| 症状 | 原因 | 対処 |
| --- | --- | --- |
| `relation "posts" does not exist` などテーブル未作成エラー | マイグレーション未実行 | 手順② `php artisan migrate` |
| 画面は出るが**画像だけ**表示されない（404） | storage リンク未作成 | 手順③ `php artisan storage:link` |
| `Class "..." not found` / 画面が真っ白 | マージ競合の解消漏れ・依存未更新 | 手順① を実行。直らなければ競合箇所を担当に相談 |
| CSS が全部外れる | `vite.config.js` の `input` に**存在しないファイル**が混ざっている | 該当行を削除／該当ファイルを作成（1つでも欠けるとビルド全体が失敗） |

---

## 補足

- **サンプルデータ（投稿など）は Git で共有されません**（DBの中身のため）。マージ後の掲示板などは最初は空です。中身が欲しい場合は、画面から投稿するか、シーダー等で各自投入してください。
- 詳しい考え方は [database-migration-guide.md](database-migration-guide.md) を参照。

---

### 迷ったら（まとめて実行してOK）

```bash
composer install
npm install --ignore-scripts
php artisan migrate
php artisan storage:link
```

上4つは、実行しても状態が変わっていなければ何も起きない安全な組み合わせです。
