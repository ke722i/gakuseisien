import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/qna.css',
                'resources/css/forum/forum-top.css',
                'resources/js/qna.js',
                'resources/css/auth.css',
<<<<<<< HEAD
                'resources/css/home.css',
=======
                'resources/css/reservation/home/student.css',
                'resources/css/reservation/home/teacher.css',
                'resources/css/reservation/room/bulk-reservation.css',
>>>>>>> develop
                'resources/css/qna/qna.css',
                'resources/css/qna/create.css',
                'resources/css/qna/detail.css',
                'resources/css/qna/history.css',
<<<<<<< HEAD
=======
                // 'resources/css/qna/profile.css', // ← ファイル未作成。作成してから有効化する（存在しないとビルド全体が失敗する）
>>>>>>> develop
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
