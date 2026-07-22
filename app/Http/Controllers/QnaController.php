<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Answer;
use App\Models\Report;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QnaController extends Controller
{
    // 質問一覧画面
    public function index(Request $request)
    {
        // ソートとキーワードを取得
        $sort = $request->input('sort', 'new');
        $keyword = $request->input('keyword');

        // 一覧では回答件数しか使わないため、件数だけを1回のクエリでまとめて取る
        $query = Question::withCount('answers');

        // 検索処理：キーワードがあればタイトルまたは内容から検索
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('content', 'LIKE', "%{$keyword}%");
            });
        }

        // ソート処理
        if ($sort === 'resolved') {
            $query->orderByRaw('best_answer_id IS NOT NULL DESC');
        } elseif ($sort === 'unresolved') {
            $query->orderByRaw('best_answer_id IS NULL DESC');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // ページネーションに検索条件とソート条件を引き継ぐ
        $posts = $query->paginate(10)->appends([
            'sort' => $sort,
            'keyword' => $keyword
        ]);

        return view('qna.qna', compact('posts', 'sort', 'keyword'));
    }

    // 質問投稿画面
    public function create()
    {
        return view('qna.create');
    }

    // 質問をデータベースに保存する
    public function store(Request $request)
    {
        // 1. 入力チェック（画像がある場合は最大2MBまでの画像ファイルのみ許可）
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category' => 'required|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 💡 画像バリデーションを追加
        ]);

        // 2. 画像の保存処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            // storage/app/public/qna フォルダに画像をユニークな名前で保存
            $imagePath = $request->file('image')->store('qna', 'public');
        }

        // 3. データベースへの保存
        Question::create([
            'school_id' => 1,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'image' => $imagePath, // 💡 データベースの image カラムに保存パスをセット
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

        // 投稿者本人または教職員のみ削除できる
        if (Auth::id() !== $question->user_id && ! Auth::user()?->isTeacher()) {
            abort(403, 'この質問を削除する権限がありません。');
        }

        $question->delete();

        // 削除が完了したら、一覧画面に戻る
        return back()->with('success', '質問を削除しました！');
    }

    //💡 リプライの削除
    public function destroyAnswer(Answer $answer)
    {
        // 投稿者本人または教職員のみ削除できる（質問の削除と条件をそろえる）
        if (Auth::id() !== $answer->user_id && ! Auth::user()?->isTeacher()) {
            abort(403, 'この操作は許可されていません。');
        }
        $answer->delete();

        return back()->with('success', 'コメントを削除しました。');
    }

    // 投稿履歴画面
    public function history()
    {
        // 一覧では回答件数しか使わないため、件数だけをまとめて取る
        $posts = Question::withCount('answers')
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->get();
        return view('qna.history', compact('posts'));
    }

    //詳細画面
    public function show($id)
    {
        // 回答は画面で投稿者・投票状況・返信をたどるため、まとめて読み込んでおく。
        // 返信は3階層先まで先読みし、それ以上の深さでも表示自体は正しく行われる。
        $nested = ['user', 'upvoters'];
        $with = array_merge($nested, [
            'replies' => fn ($q) => $q->with(array_merge($nested, [
                'replies' => fn ($q2) => $q2->with(array_merge($nested, ['replies.user', 'replies.upvoters'])),
            ])),
        ]);

        // 💡 'user' を追加しました
        $post = Question::with(['user', 'answers' => function ($query) use ($id, $with) {
            $query->whereNull('parent_id')
                ->with($with)
                // 承認済み(is_approved)を優先的に一番上に持ってくる設定
                ->orderByRaw('is_approved DESC, id = (select best_answer_id from questions where id = ?) DESC', [$id])
                ->orderBy('created_at', 'asc');
        }])->findOrFail($id);

        return view('qna.detail', compact('post'));
    }

    //コメントの保存処理
    public function storeAnswer(Request $request, $questionId)
    {
        $request->validate([
            'comment' => 'required|string',
            'parent_id' => 'nullable|integer|exists:answers,id', // parent_id のバリデーションを追加
        ]);

        // 回答の登録
        $answer = new Answer();
        $answer->question_id = $questionId;
        $answer->user_id = Auth::id();
        $answer->content = $request->input('comment');

        // 💡 リクエストに parent_id があればセットする（無ければ null になり、通常の回答になる）
        $answer->parent_id = $request->input('parent_id');

        $answer->save();

        return back()->with('success', 'コメントを投稿しました。');
    }

    public function selectBestAnswer($id, $answer_id)
    {
        // 対象の質問を取得
        $question = Question::findOrFail($id);

        // 質問の投稿者本人または教職員のみベストアンサーを選べる
        if (Auth::id() !== $question->user_id && ! Auth::user()?->isTeacher()) {
            abort(403, 'ベストアンサーを選ぶ権限がありません。');
        }

        // 別の質問に付いた回答IDを指定されても選べないよう、この質問の回答に限定する
        $answer = Answer::where('question_id', $question->id)->findOrFail($answer_id);

        // best_answer_id 列に選ばれた回答のIDを保存して更新
        $question->update([
            'best_answer_id' => $answer->id
        ]);

        // 選ばれた回答者に知らせる（気づかれないまま終わらないようにする）
        if ($answer->user_id !== Auth::id()) {
            UserNotification::send(
                $answer->user_id,
                'あなたの回答がベストアンサーに選ばれました',
                mb_strimwidth($question->title, 0, 60, '…'),
                route('qna.detail', $question->id, absolute: false)
            );
        }

        return back()->with('success', 'ベストアンサーを決定しました！');
    }

    public function reportQuestion(Request $request, $id)
    {
        $post = Question::findOrFail($id);

        if ($post->user_id === Auth::id()) {
            return redirect()->back()->with('error', '自分の投稿を通報することはできません。');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // 同じ投稿を何度も通報できないようにする
        $alreadyReported = Report::where('question_id', $post->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyReported) {
            return redirect()->back()->with('error', 'この投稿はすでに通報済みです。');
        }

        Report::create([
            'question_id' => $post->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'type' => 'question', // 通報管理一覧で掲示板と区別するため
        ]);

        return redirect()->back()->with('success', '通報を受け付けました。ご協力ありがとうございます。');
    }

    /**
     * 回答（コメント）への通報。
     * 質問への通報と別扱いにしないと、同じIDの質問への通報として記録されてしまう。
     */
    public function reportAnswer(Request $request, Answer $answer)
    {
        if ($answer->user_id === Auth::id()) {
            return redirect()->back()->with('error', '自分の投稿を通報することはできません。');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $alreadyReported = Report::where('answer_id', $answer->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($alreadyReported) {
            return redirect()->back()->with('error', 'この投稿はすでに通報済みです。');
        }

        Report::create([
            'answer_id' => $answer->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'type' => 'answer',
        ]);

        return redirect()->back()->with('success', '通報を受け付けました。ご協力ありがとうございます。');
    }

    // 2. 教職員用の通報一覧画面の表示
    // 通報管理一覧は掲示板とQ&Aをまとめて扱うため ReportAdminController へ移設した

    public function toggleUpvote(Answer $answer)
    {
        $user = Auth::user();

        // すでに投票しているか確認
        $exists = $answer->upvoters()->where('user_id', $user->id)->exists();

        if ($exists) {
            // 投票解除
            $answer->upvoters()->detach($user->id);
            $answer->decrement('upvote_count');
            $upvoted = false;
        } else {
            // 新規投票
            $answer->upvoters()->attach($user->id);
            $answer->increment('upvote_count');
            $upvoted = true;
        }

        // 最新の投票カウントをフロントに返却
        return response()->json([
            'success' => true,
            'upvoted' => $upvoted,
            'upvote_count' => $answer->fresh()->upvote_count
        ]);
    }

    public function approveAnswer(Request $request, $id)
    {
        $answer = Answer::findOrFail($id);

        if (!Auth::check() || !Auth::user()->isTeacher()) {
            return back()->with('error', '教職員のみ承認できます。');
        }

        $answer->is_approved = true;
        $answer->save();

        // 承認されたことを回答者に知らせる
        if ($answer->user_id && $answer->user_id !== Auth::id()) {
            UserNotification::send(
                $answer->user_id,
                'あなたの回答が教職員に承認されました',
                mb_strimwidth($answer->question?->title ?? '学内Q&A', 0, 60, '…'),
                route('qna.detail', $answer->question_id, absolute: false)
            );
        }

        return back()->with('success', '承認しました！');
    }
}
