<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 動作確認用アカウントの投入。
 *
 * ■ 本番の運用方針
 *   学生アカウントは教職員が名簿CSVを取り込んで一括発行する（ログインID＝学籍番号／初期パスワードはランダム）。
 *   このSeederはあくまで開発・動作確認用のため、パスワードは分かりやすい固定値にしている。
 *
 * ■ 仕様上の注意
 *   - 欠席届は「担任の class_number 前方4文字」と「学生の class_number」で振り分ける。
 *     例）担任 R4SA00 → R4SA で始まるクラスの学生の届が見える。
 *   - 届の受理・差し戻し通知は student_number からユーザーを逆引きするため、
 *     student_number は全ユーザーで重複させないこと。
 *
 * updateOrCreate を使っているので、既にDBがあるメンバーが再実行しても
 * プロフィール（クラス・氏名・担任など）が最新の内容に更新される。
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTeachers();
        $this->seedStudents();
        $this->seedLegacyAccounts();
    }

    /** 教職員アカウント（担当クラスを必ず設定する） */
    private function seedTeachers(): void
    {
        $teachers = [
            // login_id,   password,      氏名,     教員番号, 担当クラス, 担当科目
            ['katayama',   'katayama1',   '片山 誠', 'T002',  'R4SA00', '情報処理'],
            ['eguchi',     'eguchi1',     '江口 学', 'T003',  'R1SA00', '数学'],
            ['kutoku',     'kutoku1',     '久徳 明', 'T004',  'R2SC00', '英語'],
            ['furukawa',   'furukawa1',   '古川 隆', 'T005',  'R3SC00', 'ネットワーク'],
        ];

        foreach ($teachers as [$loginId, $password, $name, $number, $class, $subject]) {
            User::updateOrCreate(
                ['login_id' => $loginId],
                [
                    'password' => Hash::make($password),
                    'role' => 'teacher',
                    'must_change_password' => false,
                    'teacher_name' => $name,
                    'teacher_number' => $number,
                    'class_number' => $class,
                    'subject' => $subject,
                ],
            );
        }
    }

    /**
     * 学生アカウント（運用方針どおり ログインID＝学籍番号）。
     * 欠席届の各項目がこのプロフィールから自動入力される。
     *
     * 氏名は実在の人物と紐付かない仮名（山田太郎・佐藤花子など、フォームの
     * サンプルでよく使われる名前）にしている。
     */
    private function seedStudents(): void
    {
        $students = [
            // 学籍番号(=ID), password,   氏名,        クラス,   担任,      初回変更を求めるか
            ['234001',        'yamada1',  '山田 太郎', 'R1SA01', '江口 学', false],
            ['234002',        'sato1',    '佐藤 花子', 'R2SC01', '久徳 明', false],
            ['234003',        'suzuki1',  '鈴木 次郎', 'R3SC01', '古川 隆', false],
            ['234043',        'tanaka1',  '田中 三郎', 'R4SA09', '片山 誠', false],
            ['234054',        'takahashi1', '高橋 陽子', 'R4SA24', '片山 誠', false],
            // 初回ログイン時のパスワード変更フローを確認するためのアカウント
            ['234100',        'shinki12', '新規 太郎', 'R4SA24', '片山 誠', true],
        ];

        foreach ($students as [$studentNumber, $password, $name, $class, $homeroom, $mustChange]) {
            User::updateOrCreate(
                ['login_id' => $studentNumber],
                [
                    'password' => Hash::make($password),
                    'role' => 'student',
                    'must_change_password' => $mustChange,
                    'student_number' => $studentNumber,
                    'student_name' => $name,
                    'class_number' => $class,
                    'homeroom_teacher' => $homeroom,
                ],
            );
        }
    }

    /**
     * 以前から使っている確認用アカウント。
     * チームの動作確認手順を壊さないため残すが、上の学生アカウントと
     * student_number が重複しないよう固有の番号を割り当てる。
     */
    private function seedLegacyAccounts(): void
    {
        User::updateOrCreate(
            ['login_id' => 'student01'],
            [
                'password' => Hash::make('student1'),
                'role' => 'student',
                'must_change_password' => false,
                'student_number' => '234900',
                'student_name' => '学生 一郎',
                'class_number' => 'R4SA01',
                'homeroom_teacher' => '片山 誠',
            ],
        );

        User::updateOrCreate(
            ['login_id' => 'teacher01'],
            [
                'password' => Hash::make('teacher1'),
                'role' => 'teacher',
                'must_change_password' => false,
                'teacher_name' => '教員 太郎',
                'teacher_number' => 'T001',
                'subject' => '国語',
                'class_number' => 'R4SA00',
            ],
        );

        User::updateOrCreate(
            ['login_id' => 'Teacher'],
            [
                'password' => Hash::make('123456'),
                'role' => 'teacher',
                'must_change_password' => false,
                'teacher_name' => '管理 花子',
                'teacher_number' => 'T000',
                'subject' => '社会',
                'class_number' => 'R4SA00',
            ],
        );

        // 旧・実名ベースの学生アカウント（nishikawa など）は学籍番号版の仮名データに
        // 置き換えたため、既存DBに残っている場合は削除する。
        User::whereIn('login_id', ['nishikawa', 'kimura', 'mise', 'miyata', 'hujita'])->delete();
    }
}
