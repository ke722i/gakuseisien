{{--
    アカウント作成・編集の共通フォーム。
    $user     … 編集時は対象ユーザー、作成時は null
    $isEdit   … true=編集 / false=作成
--}}
@php($u = $user ?? null)
@php($role = old('role', $u->role ?? 'student'))

<form method="POST" action="{{ $isEdit ? route('admin.users.update', $u) : route('admin.users.store') }}" class="user-form" id="userForm">
    @csrf
    @if ($isEdit)
        @method('PATCH')
    @endif

    <div class="form-field">
        <label>ID <span style="color:#dc2626">*</span></label>
        <input type="text" name="login_id" value="{{ old('login_id', $u->login_id ?? '') }}" required>
        <p class="hint">英字と数字で6〜10文字。</p>
        @error('login_id')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-field">
        <label>権限 <span style="color:#dc2626">*</span></label>
        <select name="role" id="roleSelect" required>
            <option value="student" @selected($role === 'student')>学生</option>
            <option value="teacher" @selected($role === 'teacher')>教職員</option>
        </select>
        @error('role')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    <div class="form-field">
        <label>パスワード @unless($isEdit)<span style="color:#dc2626">*</span>@endunless</label>
        <input type="text" name="password" value="{{ old('password') }}" @unless($isEdit) required @endunless>
        <p class="hint">
            英字と数字を使い、数字を1文字以上含む6文字以上。
            @if ($isEdit) 変更しない場合は空欄のまま。@endif
        </p>
        @error('password')<p class="form-error">{{ $message }}</p>@enderror
    </div>

    {{-- 学生用プロフィール --}}
    <div class="role-fields" id="studentFields">
        <div class="form-field">
            <label>学生氏名</label>
            <input type="text" name="student_name" value="{{ old('student_name', $u->student_name ?? '') }}">
        </div>
        <div class="form-field">
            <label>学籍番号</label>
            <input type="text" name="student_number" value="{{ old('student_number', $u->student_number ?? '') }}">
        </div>
        <div class="form-field">
            <label>クラス番号</label>
            <input type="text" name="class_number" value="{{ old('class_number', $u->class_number ?? '') }}">
            <p class="hint">欠席届の担任への振り分けに使われます（例: R4SA24）。</p>
        </div>
        <div class="form-field">
            <label>担任教師</label>
            <input type="text" name="homeroom_teacher" value="{{ old('homeroom_teacher', $u->homeroom_teacher ?? '') }}">
        </div>
    </div>

    {{-- 教職員用プロフィール --}}
    <div class="role-fields" id="teacherFields">
        <div class="form-field">
            <label>教員氏名</label>
            <input type="text" name="teacher_name" value="{{ old('teacher_name', $u->teacher_name ?? '') }}">
        </div>
        <div class="form-field">
            <label>教員番号</label>
            <input type="text" name="teacher_number" value="{{ old('teacher_number', $u->teacher_number ?? '') }}">
        </div>
        <div class="form-field">
            <label>担当クラス番号</label>
            <input type="text" name="class_number_teacher" value="{{ old('class_number', $u->class_number ?? '') }}" disabled data-mirror="class_number">
            <p class="hint">欠席届は担当クラス（前方4文字一致）で絞り込まれます（例: R4SA00）。</p>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="primary-btn">{{ $isEdit ? '更新する' : '作成する' }}</button>
        <a href="{{ route('admin.users.index') }}" class="cancel-btn">キャンセル</a>
    </div>
</form>

<script>
    // 権限に応じて学生用/教職員用のプロフィール欄を出し分ける。
    // 教職員のクラス欄は student と同じ class_number カラムへ送るため、値をミラーする。
    (function () {
        const roleSelect = document.getElementById('roleSelect');
        const studentFields = document.getElementById('studentFields');
        const teacherFields = document.getElementById('teacherFields');
        const teacherClass = document.querySelector('[data-mirror="class_number"]');
        const studentClass = document.querySelector('#studentFields [name="class_number"]');

        function sync() {
            const isTeacher = roleSelect.value === 'teacher';
            studentFields.style.display = isTeacher ? 'none' : 'block';
            teacherFields.style.display = isTeacher ? 'block' : 'none';

            // 送信されるのは表示中の class_number のみにしたいので、非表示側を無効化
            studentClass.disabled = isTeacher;
            if (teacherClass) {
                teacherClass.disabled = !isTeacher;
                teacherClass.name = isTeacher ? 'class_number' : 'class_number_teacher';
                if (isTeacher) teacherClass.value = teacherClass.value || studentClass.value;
            }
        }

        roleSelect.addEventListener('change', sync);
        sync();
    })();
</script>
