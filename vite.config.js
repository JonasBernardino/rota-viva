import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
    ],
    server: {
        host: 'localhost',
        hmr: {
            host: 'localhost',
        },
        allowedHosts: [
            'localhost',
            '127.0.0.1',
            'rota-viva.test',
            '.rota-viva.test',
            '.test',
        ],
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
