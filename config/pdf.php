<?php

return [
    /*
     * The disk where the PDFs will be stored.
     */
    'disk' => 'local',

    /*
     * The path where the PDFs will be stored.
     */
    'path' => 'pdfs',

    /*
     * The paper format.
     */
    'format' => 'a4',

    /*
     * The paper orientation.
     */
    'orientation' => 'portrait',

    /*
     * The margins.
     */
    'margins' => [
        'top' => 10,
        'right' => 10,
        'bottom' => 10,
        'left' => 10,
    ],

    /*
     * The browser options.
     */
    'browser' => [
        'node' => env('NODE_PATH', '/usr/bin/node'),
        'npm' => env('NPM_PATH', '/usr/bin/npm'),
    ],
];
