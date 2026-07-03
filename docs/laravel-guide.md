# Laravel の基本メモ

このファイルは、このリポジトリで「どこに何を書くか」を迷ったときの判断基準を、できるだけ具体的にまとめたものです。

## このプロジェクトの今の動き

現時点では、ブラウザで `/` にアクセスすると [routes/web.php](../routes/web.php) の設定が動き、[resources/views/welcome.blade.php](../resources/views/welcome.blade.php) を返します。

つまり今は、次のような最小構成です。

1. URL を受け取る
2. ルートで返す画面を決める
3. View をそのまま表示する

Controller や Model はまだ使っていません。画面の見た目は [resources/css/app.css](../resources/css/app.css)、少しの動きは [resources/js/app.js](../resources/js/app.js) に書いています。

## Laravel はどう動くか

Laravel は、URL から画面や処理へたどる仕組みです。基本の流れは次の通りです。

1. ブラウザから URL にアクセスする
2. [routes/web.php](../routes/web.php) で、その URL に対して何を返すか決める
3. 複雑な処理があれば Controller に渡す
4. DB を使うなら Model で読み書きする
5. DB の構造は Migration で管理する
6. 最後に View を返して画面を表示する

この分け方をしておくと、「表示だけ変えたい」「DB を追加したい」「入力チェックを入れたい」などの変更が整理しやすくなります。

## 何をどこに書くか

### 1. routes/web.php

URL と画面の入口を置きます。

たとえば、トップページを表示したいならここに書きます。

```php
Route::get('/', function () {
	return view('welcome');
});
```

将来、`/news` や `/board` のような画面を追加するときも、まずここに URL を足します。

### 2. Controller

画面の前処理、入力チェック、DB 取得結果のまとめ方などを置きます。

たとえば、ニュース一覧を DB から取って画面に渡すなら、Controller に書くのが基本です。

Controller を使うときのイメージは次の通りです。

```php
public function index()
{
	$news = News::latest()->get();

	return view('news.index', compact('news'));
}
```

### 3. Model

DB の1行を表す役割です。テーブルからの取得、保存、更新、削除を扱います。

たとえば `news` テーブルを扱うなら `News` Model を作ります。

Model には、次のようなものを置きます。

1. テーブル名に対応するクラス
2. 取得や保存に使うプロパティ設定
3. 他のテーブルとの関連

### 4. Migration

DB のテーブル定義を置きます。

たとえば、「ニュースのタイトル、本文、カテゴリ、公開日」を保存したいなら、Migration でそのカラムを作ります。

Migration は、後から見ても「このテーブルは何のために作ったか」が分かるようにしておくと便利です。

### 5. View

画面に見せる HTML を置きます。

このリポジトリでは、現在 [resources/views/welcome.blade.php](../resources/views/welcome.blade.php) がそれに当たります。

View には、次のものを書きます。

1. 画面の見出し
2. 一覧やフォーム
3. Controller から受け取った値の表示

### 6. CSS

見た目の調整を [resources/css/app.css](../resources/css/app.css) に書きます。

たとえば、色、余白、文字サイズ、スマホ表示の調整などです。

今のこのプロジェクトでは、サイドバー、カード、ボタン、スマホ表示の切り替えがここにまとまっています。

### 7. JavaScript

[resources/js/app.js](../resources/js/app.js) には、ボタンの開閉、表示切り替え、軽い画面操作を書きます。

今のこのプロジェクトでは、メニューの開閉とニュースカードの展開がここです。

## 画面を追加するときの考え方

新しい機能を作るときは、次の順番で考えると迷いにくいです。

1. まず URL を決める
2. その URL で何を表示するか決める
3. DB が必要ならテーブルを作る
4. 取得や保存の処理を Controller と Model に分ける
5. 最後に View と CSS で画面を整える

たとえば「掲示板」を追加するなら、次のように分けます。

1. `/board` を [routes/web.php](../routes/web.php) に追加する
2. `BoardController` を作る
3. `boards` テーブルの Migration を作る
4. `Board` Model を作る
5. 画面用の View を作る。たとえば既存の [resources/views/welcome.blade.php](../resources/views/welcome.blade.php) のように Blade ファイルを置く

## README に残すべき内容

README には、細かい実装方法よりも「初めて触る人が最初に知るべきこと」を残すと見やすいです。

入れておくとよいのは次の内容です。

1. アプリ全体の目的
2. 起動手順
3. 必要な環境変数
4. 画面や機能の一覧
5. 詳細説明へのリンク
6. 開発ルールや運用ルール

細かい仕組みや実装の考え方は、このファイルのように別ページへ分けると README が読みやすくなります。