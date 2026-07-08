const teacherList = document.getElementById('teacher-list');
const addTeacherButton = document.querySelector('.teacher-btn.add');
const removeTeacherButton = document.querySelector('.teacher-btn.remove');

if (teacherList && addTeacherButton && removeTeacherButton) {
    const maxTeachers = 4;

    const updateTeacherButtons = () => {
        const rows = teacherList.querySelectorAll('.teacher-row');
        addTeacherButton.disabled = rows.length >= maxTeachers;
        removeTeacherButton.disabled = rows.length <= 1;
    };

    addTeacherButton.addEventListener('click', () => {
        const rows = teacherList.querySelectorAll('.teacher-row');
        if (rows.length >= maxTeachers) {
            return;
        }

        const row = document.createElement('div');
        row.className = 'teacher-row';
        row.innerHTML = `
            <select name="subject_teachers[]">
                <option>科目教師1</option>
                <option>担当教師2</option>
                <option>担当教師3</option>
                <option>担当教師4</option>
            </select>
        `;

        teacherList.appendChild(row);
        updateTeacherButtons();
    });

    removeTeacherButton.addEventListener('click', () => {
        const rows = teacherList.querySelectorAll('.teacher-row');
        if (rows.length <= 1) {
            return;
        }

        rows[rows.length - 1].remove();
        updateTeacherButtons();
    });

    updateTeacherButtons();
}