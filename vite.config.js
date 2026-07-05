import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin-layout.css',
                'resources/css/admin-elections.css',
                'resources/css/admin-results.css',
                'resources/css/admin-voter-show.css',
                'resources/css/voter-layout.css',
                'resources/css/voter-login.css',
                'resources/css/voter-register.css',
                'resources/css/voter-password.css',
                'resources/css/voter-otp.css',
                'resources/css/voter-ballot.css',
                'resources/css/voter-confirmation.css',
                'resources/js/app.js',
                'resources/js/admin-layout.js',
                'resources/js/admin-voters-filter.js',
                'resources/js/admin-voters-assign.js',
                'resources/js/voter-login.js',
                'resources/js/voter-password.js',
                'resources/js/voter-otp.js',
                'resources/js/voter-ballot.js',
            ],
            refresh: true,
        }),
    ]
});
