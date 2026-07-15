<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QnaController extends Controller
{
    // 質問一覧画面
    public function index()
    {
        $posts = Question::latest()->get();
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
        // 入力チェック
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required|max:50',
        ]);

        // 💡 user_id を 1 固定から、現在ログインしているユーザーのIDに変更します！
        Question::create([
            'school_id' => 1,
            'user_id' => Auth::id() ?? 1, // ログイン中ならそのID、未ログインなら仮で1
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
    public function storeAnswer(Request $request, $id)
    {
        $post = Question::findOrFail($id);

        // 💡 既にベストアンサーが選ばれている場合は、処理を中断して追い返す
        if ($post->best_answer_id) {
            return back()->with('error', 'この質問はすでに解決済みのため、コメントできません。');
        }

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        Answer::create([
            'question_id' => $id,
            'user_id'     => Auth::id() ?? 1,
            'content'     => $request->comment,
        ]);

        return redirect()->route('qna.detail', $id)->with('success', 'コメントを投稿しました');
    }

    public function selectBestAnswer($id, $answer_id)
    {
        // 対象の質問を取得
        $question = Question::findOrFail($id);

        // best_answer_id 列に選ばれた回答のIDを保存して更新
        $question->update([
            'best_answer_id' => $answer_id
        ]);

        return back()->with('success', 'ベストアンサーを決定しました！');
    }

    public function reportQuestion(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        Report::create([
            'question_id' => $id,
            'user_id'     => Auth::id(), // ログイン中ならIDが入る
            'reason'      => $request->reason,
        ]);

        return back()->with('success', '通報を受け付けました。ご協力ありがとうございます。');
    }

    // 2. 教職員用の通報一覧画面の表示
    public function adminReports()
    {
        $reports = Report::with('question')->latest()->get();
        return view('qna.admin_reports', compact('reports'))->with('active', 'admin_reports');
    }
}
