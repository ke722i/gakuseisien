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
                'resources/js/qna.js',
                'resources/css/auth.css',
                'resources/css/home.css',
                'resources/css/forum/forum-top.css',
                'resources/css/forum/forum-create.css',
                'resources/css/notification.css',
                'resources/js/notification.js',
                'resources/css/reservation/home/student.css',
                'resources/css/reservation/home/teacher.css',
                'resources/css/reservation/room/bulk-reservation.css',
                'resources/css/qna/qna.css',
                'resources/css/qna/create.css',
                'resources/css/qna/detail.css',
                'resources/css/qna/history.css',
                'resources/css/store/home.css',
                'resources/css/store/more.css',
                'resources/css/store/request.css',
                'resources/css/event/calendar.css',
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
