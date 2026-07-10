<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QnaController extends Controller
{
    // 質問一覧画面
    public function index()
    {
        // データベースから最新の質問をすべて取得して一覧画面に渡す
        $posts = Question::latest('created_at')->get();
        return view('qna.qna', compact('posts'));
    }

    // 質問投稿画面
    public function create()
    {
        return view('qna.create');
    }

    // 質問をデータベースに保存する
    public function store(Request $request)
    {
        // 入力チェック（要件定義のカラムに合わせる）
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required|max:50',
        ]);

        // ログインや大学判別機能が完成するまでは、テスト用に school_id=1, user_id=1 として仮保存
        Question::create([
            'school_id' => 1,
            'user_id' => 1,
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'best_answer_id' => null,
        ]);

        // 保存が完了したら、一覧画面へ戻る
        return redirect()->route('gakunai.qna')->with('success', '質問を投稿しました！');
    }

    // 💡 質問をデータベースから削除する
    public function destroy($id)
    {
        // 指定されたIDの質問を探し、なければ404エラーを出す
        $question = Question::findOrFail($id);

        // 💡 今後は「自分が投稿した質問だけ消せる」というチェックをここに入れますが、
        // 現段階では開発用に誰でも消せるようにしておきます。
        $question->delete();

        // 削除が完了したら、一覧画面に戻る
        return back()->with('success', '質問を削除しました！');
    }

    // 投稿履歴画面
    public function history()
    {
        // 💡 現時点ではテストとして、最新の質問をすべて取得して履歴画面に渡します
        // (後ほどユーザーログイン機能を実装したら、自分の投稿だけに絞り込む処理に変更できます)
        $posts = Question::latest('created_at')->get();

        // view の引数に compact('posts') を渡すことで、Blade側で $posts が使えるようになります！
        return view('qna.history', compact('posts'));
    }

    //詳細画面
    public function show($id)
    {
        $post = Question::with('answers')->findOrFail($id);

        return view('qna.detail', compact('post'));
    }

    //コメントの保存処理
    // コメントの保存処理
    public function storeAnswer(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // 回答データをanswersテーブルに保存
        Answer::create([
            'question_id' => $id,
            'user_id'     => Auth::id() ?? null, 
            'content'     => $request->comment,
        ]);

        // 書き込んだ詳細画面にそのままリダイレクトで戻る
        return redirect()->route('qna.detail', $id)->with('success', 'コメントを投稿しました');
    }
}
