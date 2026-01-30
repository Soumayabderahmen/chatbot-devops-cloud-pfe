<?php

return [
    // Dossier de build (relatif à public/)
    'build_path' => 'build',

    // ATTENTION : Laravel lit le manifest à la racine de build (copié par CI/Docker)
    'manifest' => public_path('build/manifest.json'),

    'entrypoints' => [
        'resources/css/app.css',
        'resources/js/app.js',
    ],
];
