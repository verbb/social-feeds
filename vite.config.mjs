import { resolve } from 'node:path';

import { defineConfig } from 'vite';

const assets = resolve(import.meta.dirname, 'src/web/assets');

const builds = {
    'cp-css': {
        root: resolve(assets, 'cp'),
        input: 'social-feeds-cp.css',
        isCss: true,
        clean: true,
    },
    'cp-js': {
        root: resolve(assets, 'cp'),
        input: 'social-feeds-cp.js',
    },
    'frontend-css': {
        root: resolve(assets, 'frontend'),
        input: {
            'social-feeds': 'social-feeds.css',
            'social-feeds-masonry': 'social-feeds-masonry.css',
        },
        isCss: true,
        clean: true,
    },
    'masonry-js': {
        root: resolve(assets, 'frontend'),
        input: 'social-feeds-masonry.js',
    },
};

export default defineConfig(({ mode }) => {
    const config = builds[mode];

    if (!config) {
        throw new Error(`Unknown asset build mode: ${mode}`);
    }

    const input = typeof config.input === 'string'
        ? resolve(config.root, 'src', config.input)
        : Object.fromEntries(Object.entries(config.input).map(([name, file]) => [name, resolve(config.root, 'src', file)]));

    const output = config.isCss ? {
        assetFileNames: '[name][extname]',
    } : {
        codeSplitting: false,
        entryFileNames: '[name].js',
        format: 'iife',
    };

    return {
        root: config.root,
        build: {
            outDir: resolve(config.root, 'dist'),
            emptyOutDir: config.clean ?? false,
            assetsDir: '',
            cssMinify: 'esbuild',
            cssTarget: ['chrome61', 'safari10'],
            minify: 'oxc',
            sourcemap: !config.isCss,
            target: 'es2015',
            rolldownOptions: {
                input,
                output,
            },
        },
    };
});
